<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\Notification;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:appointments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send 1-hour and 24-hour appointment reminders to doctors and patients';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // 1-hour reminders: appointment_date = today AND start_time is between now+55m and now+65m
        $oneHourStart = $now->copy()->addMinutes(55)->format('H:i:s');
        $oneHourEnd = $now->copy()->addMinutes(65)->format('H:i:s');

        $oneHourAppointments = Appointment::where('status', 'confirmed')
            ->where('appointment_date', $now->toDateString())
            ->whereBetween('start_time', [$oneHourStart, $oneHourEnd])
            ->get();

        foreach ($oneHourAppointments as $appt) {
            $patient = $appt->patient;
            $doctor = $appt->doctor;

            // Notify patient
            Notification::create([
                'user_id' => $patient->id,
                'actor_id' => $doctor->id,
                'type' => 'appointment_reminder_1hour',
                'data' => [
                    'appointment_id' => $appt->id,
                    'doctor_name' => $doctor->display_name,
                    'patient_name' => $patient->display_name,
                    'time' => $appt->start_time,
                    'type' => 'appointment_reminder_1hour'
                ]
            ]);

            // Notify doctor
            Notification::create([
                'user_id' => $doctor->id,
                'actor_id' => $patient->id,
                'type' => 'appointment_reminder_1hour',
                'data' => [
                    'appointment_id' => $appt->id,
                    'doctor_name' => $doctor->display_name,
                    'patient_name' => $patient->display_name,
                    'time' => $appt->start_time,
                    'type' => 'appointment_reminder_1hour'
                ]
            ]);
        }

        // 24-hour reminders: appointment_date = tomorrow
        $tomorrow = $now->copy()->addDay()->toDateString();
        
        $twentyFourHourAppointments = Appointment::where('status', 'confirmed')
            ->where('appointment_date', $tomorrow)
            ->get();

        foreach ($twentyFourHourAppointments as $appt) {
            $patient = $appt->patient;
            $doctor = $appt->doctor;

            // Notify patient
            Notification::create([
                'user_id' => $patient->id,
                'actor_id' => $doctor->id,
                'type' => 'appointment_reminder_24hours',
                'data' => [
                    'appointment_id' => $appt->id,
                    'doctor_name' => $doctor->display_name,
                    'patient_name' => $patient->display_name,
                    'time' => $appt->start_time,
                    'type' => 'appointment_reminder_24hours'
                ]
            ]);

            // Notify doctor
            Notification::create([
                'user_id' => $doctor->id,
                'actor_id' => $patient->id,
                'type' => 'appointment_reminder_24hours',
                'data' => [
                    'appointment_id' => $appt->id,
                    'doctor_name' => $doctor->display_name,
                    'patient_name' => $patient->display_name,
                    'time' => $appt->start_time,
                    'type' => 'appointment_reminder_24hours'
                ]
            ]);
        }

        $total = $oneHourAppointments->count() + $twentyFourHourAppointments->count();
        $this->info("Sent {$total} appointment reminders.");
    }
}
