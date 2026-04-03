<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\HelpRequest;
use App\Models\HelpRequestMessage;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HelpRequestController extends Controller
{
    private function layout(): string
    {
        return match (auth()->user()->role) {
            'doctor' => 'layouts.doctor',
            'admin'  => 'layouts.admin',
            default  => 'layouts.patient',
        };
    }

    /** GET /help-requests — list requests (patient = sent, doctor = received) */
    public function index(): View
    {
        $user   = auth()->user();
        $layout = $this->layout();

        if ($user->role === 'doctor') {
            $requests = HelpRequest::where('doctor_id', $user->id)
                ->with(['user', 'messages'])
                ->latest()
                ->paginate(15);
        } else {
            $requests = HelpRequest::where('user_id', $user->id)
                ->with(['doctor', 'messages'])
                ->latest()
                ->paginate(15);
        }

        return view('shared.help-requests.index', compact('requests', 'layout'));
    }

    /** GET /help-requests/create */
    public function create(): View
    {
        abort_unless(auth()->user()->role === 'patient', 403);

        // Get approved doctors for selection
        $doctors = User::where('role', 'doctor')
            ->where('doctor_status', 'approved')
            ->orderBy('fname')
            ->get();

        $doctorId = request('doctor_id');
        $layout   = $this->layout();

        return view('shared.help-requests.create', compact('doctors', 'doctorId', 'layout'));
    }

    /** POST /help-requests — create a new help request */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        abort_unless(auth()->user()->role === 'patient', 403);

        $request->validate([
            'doctor_id'       => ['required', 'exists:users,id'],
            'suggested_title' => ['required', 'string', 'max:200'],
        ]);

        $doctor = User::where('id', $request->doctor_id)
            ->where('role', 'doctor')
            ->firstOrFail();

        $helpRequest = HelpRequest::create([
            'user_id'         => auth()->id(),
            'doctor_id'       => $doctor->id,
            'suggested_title' => $request->suggested_title,
            'status'          => 'pending',
        ]);

        // Notify the doctor
        Notification::create([
            'user_id'  => $doctor->id,
            'actor_id' => auth()->id(),
            'type'     => 'help_request',
            'data'     => [
                'help_request_id' => $helpRequest->id,
                'title'           => $helpRequest->suggested_title,
                'url'             => route('help-requests.show', $helpRequest->id),
            ],
        ]);

        return redirect()->route('help-requests.show', $helpRequest->id)
            ->with('success', 'Help request sent to Dr. ' . $doctor->display_name);
    }

    /** GET /help-requests/{helpRequest} */
    public function show(HelpRequest $helpRequest): View
    {
        $user = auth()->user();

        // Must be the patient or the doctor
        abort_unless(
            $helpRequest->user_id === $user->id || $helpRequest->doctor_id === $user->id,
            403
        );

        $messages = $helpRequest->messages()->with('sender')->oldest()->get();
        $layout   = $this->layout();

        return view('shared.help-requests.show', compact('helpRequest', 'messages', 'layout'));
    }

    /** POST /help-requests/{helpRequest}/accept */
    public function accept(HelpRequest $helpRequest): JsonResponse
    {
        abort_unless(auth()->user()->role === 'doctor', 403);
        abort_unless($helpRequest->doctor_id === auth()->id(), 403);
        abort_unless($helpRequest->status === 'pending', 422);

        $helpRequest->update(['status' => 'accepted']);

        // Notify patient
        Notification::create([
            'user_id'  => $helpRequest->user_id,
            'actor_id' => auth()->id(),
            'type'     => 'help_request_accepted',
            'data'     => [
                'help_request_id' => $helpRequest->id,
                'url'             => route('help-requests.show', $helpRequest->id),
            ],
        ]);

        return response()->json(['success' => true, 'status' => 'accepted']);
    }

    /** POST /help-requests/{helpRequest}/decline */
    public function decline(HelpRequest $helpRequest): JsonResponse
    {
        abort_unless(auth()->user()->role === 'doctor', 403);
        abort_unless($helpRequest->doctor_id === auth()->id(), 403);
        abort_unless($helpRequest->status === 'pending', 422);

        $helpRequest->update(['status' => 'declined']);

        // Notify patient
        Notification::create([
            'user_id'  => $helpRequest->user_id,
            'actor_id' => auth()->id(),
            'type'     => 'help_request_declined',
            'data'     => [
                'help_request_id' => $helpRequest->id,
                'url'             => route('help-requests.show', $helpRequest->id),
            ],
        ]);

        return response()->json(['success' => true, 'status' => 'declined']);
    }

    /** POST /help-requests/{helpRequest}/resolve */
    public function resolve(HelpRequest $helpRequest): JsonResponse
    {
        abort_unless(auth()->user()->role === 'doctor', 403);
        abort_unless($helpRequest->doctor_id === auth()->id(), 403);
        abort_unless($helpRequest->status === 'accepted', 422);

        $helpRequest->update([
            'status'      => 'resolved',
            'resolved_at' => now(),
        ]);

        return response()->json(['success' => true, 'status' => 'resolved']);
    }

    /** POST /help-requests/{helpRequest}/message — send a message in the thread */
    public function sendMessage(Request $request, HelpRequest $helpRequest): JsonResponse
    {
        $user = auth()->user();

        abort_unless(
            $helpRequest->user_id === $user->id || $helpRequest->doctor_id === $user->id,
            403
        );
        abort_unless(in_array($helpRequest->status, ['pending', 'accepted']), 422);

        $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = HelpRequestMessage::create([
            'help_request_id' => $helpRequest->id,
            'sender_user_id'  => $user->id,
            'body'            => $request->body,
        ]);

        $message->load('sender');

        return response()->json([
            'success' => true,
            'message' => [
                'id'          => $message->id,
                'body'        => $message->body,
                'sender_name' => $message->sender->display_name,
                'is_own'      => true,
                'created_at'  => $message->created_at->format('g:i A'),
            ],
        ]);
    }
}
