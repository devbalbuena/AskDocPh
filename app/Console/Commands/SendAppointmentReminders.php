<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    protected $signature   = 'appointments:send-reminders';
    protected $description = 'Send 24-hour and 1-hour appointment reminders to patients and doctors';

    public function handle(): int
    {
        $now       = Carbon::now();
        $in24h     = $now->copy()->addHours(24);
        $in1h      = $now->copy()->addHour();
        $windows   = [
            ['label' => '24h', 'start' => $in24h->copy()->subMinutes(10), 'end' => $in24h->copy()->addMinutes(10)],
            ['label' => '1h',  'start' => $in1h->copy()->subMinutes(10),  'end' => $in1h->copy()->addMinutes(10)],
        ];

        $sent = 0;

        foreach ($windows as $window) {
            $appointments = Appointment::with(['patient', 'doctor'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereDate('appointment_date', $window['start']->toDateString())
                ->get()
                ->filter(function (Appointment $appt) use ($window) {
                    // Combine appointment_date + start_time into a Carbon datetime
                    $apptDatetime = Carbon::parse(
                        $appt->appointment_date->toDateString() . ' ' . $appt->start_time
                    );
                    return $apptDatetime->between($window['start'], $window['end']);
                });

            foreach ($appointments as $appt) {
                $label = $window['label'];

                // Deduplication: check if reminder already sent
                $patientDupe = Notification::where('user_id', $appt->patient_id)
                    ->where('type', 'appointment_reminder')
                    ->whereJsonContains('data->appointment_id', $appt->id)
                    ->whereJsonContains('data->window', $label)
                    ->exists();

                $doctorDupe = Notification::where('user_id', $appt->doctor_id)
                    ->where('type', 'appointment_reminder')
                    ->whereJsonContains('data->appointment_id', $appt->id)
                    ->whereJsonContains('data->window', $label)
                    ->exists();

                $apptDatetime = Carbon::parse(
                    $appt->appointment_date->toDateString() . ' ' . $appt->start_time
                );

                $data = [
                    'appointment_id'   => $appt->id,
                    'window'           => $label,
                    'appointment_date' => $appt->appointment_date->format('M d, Y'),
                    'start_time'       => Carbon::parse($appt->start_time)->format('g:i A'),
                    'countdown'        => $label === '24h' ? 'in 24 hours' : 'in 1 hour',
                    'url'              => '#',
                ];

                if (!$patientDupe && $appt->patient) {
                    Notification::create([
                        'user_id'  => $appt->patient_id,
                        'actor_id' => null,
                        'type'     => 'appointment_reminder',
                        'data'     => array_merge($data, [
                            'message' => "Your appointment with Dr. {$appt->doctor?->display_name} is {$data['countdown']} ({$data['appointment_date']} at {$data['start_time']}).",
                        ]),
                    ]);
                    $sent++;
                }

                if (!$doctorDupe && $appt->doctor) {
                    Notification::create([
                        'user_id'  => $appt->doctor_id,
                        'actor_id' => null,
                        'type'     => 'appointment_reminder',
                        'data'     => array_merge($data, [
                            'message' => "Appointment with {$appt->patient?->display_name} is {$data['countdown']} ({$data['appointment_date']} at {$data['start_time']}).",
                        ]),
                    ]);
                    $sent++;
                }
            }
        }

        $this->info("Sent {$sent} appointment reminder notifications.");
        return self::SUCCESS;
    }
}
