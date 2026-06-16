<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MessagingController extends Controller
{
    /** Helper: resolve layout string from role */
    private function layout(): string
    {
        return match (auth()->user()->role) {
            'doctor' => 'layouts.doctor',
            'admin'  => 'layouts.admin',
            default  => 'layouts.patient',
        };
    }

    /** Helper: verify auth user is a participant, abort 403 otherwise */
    private function verifyParticipant(Conversation $conversation): ConversationParticipant
    {
        $participant = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', auth()->id())
            ->whereNull('deleted_at')
            ->first();

        abort_unless($participant !== null, 403, 'You are not a participant in this conversation.');
        return $participant;
    }

    /** Helper: format a message for JSON response */
    private function formatMessage(Message $message, bool $isOwn): array
    {
        $sender = $message->sender;
        $photo  = $sender?->profile_photo ? Storage::url($sender->profile_photo) : null;

        return [
            'id'           => $message->id,
            'body'         => $message->body,
            'sender_name'  => $sender?->display_name ?? 'Unknown',
            'sender_avatar'=> $photo,
            'sender_role'  => $sender?->role ?? '',
            'created_at'   => $message->created_at->format('g:i A'),
            'created_date' => $message->created_at->toDateString(),
            'is_own'       => $isOwn,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────

    /** GET /messages — conversation list */
    public function index(): View
    {
        $userId = auth()->id();

        // Get all conversation IDs for this user
        $participantRows = ConversationParticipant::where('user_id', $userId)
            ->whereNull('deleted_at')
            ->get()
            ->keyBy('conversation_id');

        $conversationIds = $participantRows->keys();

        // Load conversations ordered by latest message
        $conversations = Conversation::whereIn('id', $conversationIds)
            ->with(['messages' => fn ($q) => $q->latest()->limit(1), 'participants.user'])
            ->get()
            ->sortByDesc(fn ($c) => $c->messages->first()?->created_at ?? $c->created_at)
            ->values();

        $conversationData = $conversations->map(function (Conversation $convo) use ($userId, $participantRows) {
            // Find the other participant
            $other = $convo->participants
                ->where('user_id', '!=', $userId)
                ->first()?->user;

            $lastMsg      = $convo->messages->first();
            $myParticipant = $participantRows[$convo->id] ?? null;

            // Count unread: messages newer than last_read_message_id
            $unreadCount = 0;
            if ($lastMsg && $myParticipant) {
                $lastReadId = $myParticipant->last_read_message_id;
                $unreadCount = Message::where('conversation_id', $convo->id)
                    ->where('id', '>', $lastReadId ?? 0)
                    ->where('sender_user_id', '!=', $userId)
                    ->whereNull('deleted_at')
                    ->count();
            }

            return [
                'id'           => $convo->id,
                'other_user'   => $other ? [
                    'id'           => $other->id,
                    'name'         => $other->display_name,
                    'role'         => $other->role,
                    'is_verified_doctor' => $other->isVerifiedDoctor(),
                    'avatar'       => $other->profile_photo ? Storage::url($other->profile_photo) : null,
                    'username'     => $other->username,
                ] : null,
                'last_message' => $lastMsg ? [
                    'body'       => mb_substr($lastMsg->body ?? '', 0, 45),
                    'time'       => $lastMsg->created_at->diffForHumans(null, true, true),
                ] : null,
                'unread_count' => $unreadCount,
            ];
        });

        $layout = $this->layout();
        return view('shared.messaging.index', compact('conversationData', 'layout'));
    }

    /** GET /messages/{conversation} — show a conversation thread */
    public function show(Conversation $conversation): View
    {
        $myParticipant = $this->verifyParticipant($conversation);

        // Load all messages with sender, ordered oldest first
        $messages = Message::where('conversation_id', $conversation->id)
            ->whereNull('deleted_at')
            ->with('sender')
            ->oldest()
            ->get()
            ->map(fn ($m) => $this->formatMessage($m, $m->sender_user_id === auth()->id()));

        // Mark as read
        $lastMsg = Message::where('conversation_id', $conversation->id)->whereNull('deleted_at')->latest()->first();
        if ($lastMsg) {
            $myParticipant->update(['last_read_message_id' => $lastMsg->id]);
        }

        // Sidebar conversations list
        $userId = auth()->id();
        $participantRows = ConversationParticipant::where('user_id', $userId)->whereNull('deleted_at')->get()->keyBy('conversation_id');
        $conversations = Conversation::whereIn('id', $participantRows->keys())
            ->with(['messages' => fn ($q) => $q->latest()->limit(1), 'participants.user'])
            ->get()
            ->sortByDesc(fn ($c) => $c->messages->first()?->created_at ?? $c->created_at)
            ->values();
        $conversationData = $conversations->map(function (Conversation $convo) use ($userId, $participantRows) {
            $other = $convo->participants->where('user_id', '!=', $userId)->first()?->user;
            $lastMsg = $convo->messages->first();
            $myP = $participantRows[$convo->id] ?? null;
            $unreadCount = 0;
            if ($lastMsg && $myP) {
                $unreadCount = Message::where('conversation_id', $convo->id)
                    ->where('id', '>', $myP->last_read_message_id ?? 0)
                    ->where('sender_user_id', '!=', $userId)
                    ->whereNull('deleted_at')->count();
            }
            return [
                'id'         => $convo->id,
                'other_user' => $other ? [
                    'id' => $other->id, 'name' => $other->display_name,
                    'role' => $other->role,
                    'avatar' => $other->profile_photo ? Storage::url($other->profile_photo) : null,
                    'username' => $other->username,
                ] : null,
                'last_message' => $lastMsg ? ['body' => mb_substr($lastMsg->body ?? '', 0, 45), 'time' => $lastMsg->created_at->diffForHumans(null, true, true)] : null,
                'unread_count' => $unreadCount,
            ];
        });

        // Other participant info
        $otherParticipant = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', '!=', auth()->id())
            ->with('user')
            ->first()?->user;

        $layout = $this->layout();
        return view('shared.messaging.show', compact('conversation', 'messages', 'conversationData', 'otherParticipant', 'layout'));
    }

    /** POST /messages/start — start or find a direct conversation */
    public function start(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'recipient_id' => ['required', 'exists:users,id'],
        ]);

        $recipientId = (int) $request->recipient_id;
        $authUser    = auth()->user();

        // Cannot message yourself
        if ($recipientId === $authUser->id) {
            return response()->json(['message' => 'You cannot message yourself.'], 422);
        }

        $recipient = User::findOrFail($recipientId);

        // Messaging rules: patient/doctor cannot START conversation with admin
        if (in_array($authUser->role, ['patient', 'doctor']) && $recipient->role === 'admin') {
            // Check if admin already started a conversation with this user
            $existingIds = ConversationParticipant::where('user_id', $authUser->id)
                ->whereNull('deleted_at')
                ->pluck('conversation_id');

            $adminStartedConvo = ConversationParticipant::whereIn('conversation_id', $existingIds)
                ->where('user_id', $recipientId)
                ->whereNull('deleted_at')
                ->exists();

            if (!$adminStartedConvo) {
                return response()->json([
                    'message' => 'You cannot start a conversation with an admin.',
                ], 403);
            }
        }

        // Check if direct conversation already exists between these two users
        $myConversationIds = ConversationParticipant::where('user_id', $authUser->id)
            ->whereNull('deleted_at')
            ->pluck('conversation_id');

        $existingConversation = ConversationParticipant::whereIn('conversation_id', $myConversationIds)
            ->where('user_id', $recipientId)
            ->whereNull('deleted_at')
            ->first();

        if ($existingConversation) {
            return response()->json([
                'conversation_id' => $existingConversation->conversation_id,
                'redirect'        => route('messages.show', $existingConversation->conversation_id),
            ]);
        }

        // Create new conversation
        $conversation = Conversation::create(['type' => 'direct']);

        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id'         => $authUser->id,
            'joined_at'       => now(),
        ]);

        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id'         => $recipientId,
            'joined_at'       => now(),
        ]);

        return response()->json([
            'conversation_id' => $conversation->id,
            'redirect'        => route('messages.show', $conversation->id),
        ]);
    }

    /** POST /messages/{conversation}/send — send a message */
    public function send(Request $request, Conversation $conversation): JsonResponse
    {
        $this->verifyParticipant($conversation);

        $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'conversation_id'  => $conversation->id,
            'sender_user_id'   => auth()->id(),
            'message_type'     => 'text',
            'body'             => $request->body,
        ]);

        // Touch conversation updated_at
        $conversation->touch();

        // Update sender's last_read_message_id
        ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', auth()->id())
            ->update(['last_read_message_id' => $message->id]);

        $message->load('sender');

        return response()->json($this->formatMessage($message, true));
    }

    /** GET /messages/{conversation}/poll — long-poll for new messages */
    public function poll(Request $request, Conversation $conversation): JsonResponse
    {
        $myParticipant = $this->verifyParticipant($conversation);

        $afterId = (int) ($request->query('after_id', 0));

        $messages = Message::where('conversation_id', $conversation->id)
            ->where('id', '>', $afterId)
            ->whereNull('deleted_at')
            ->with('sender')
            ->oldest()
            ->get()
            ->map(fn ($m) => $this->formatMessage($m, $m->sender_user_id === auth()->id()));

        // Update last_read_message_id if there are new messages
        if ($messages->isNotEmpty()) {
            $lastId = $messages->last()['id'];
            $myParticipant->update(['last_read_message_id' => $lastId]);
        }

        return response()->json($messages);
    }

    /** GET /messages/unread-count — count conversations with unread messages */
    public function unreadCount(): JsonResponse
    {
        $userId = auth()->id();

        $participantRows = ConversationParticipant::where('user_id', $userId)
            ->whereNull('deleted_at')
            ->get();

        $count = 0;
        foreach ($participantRows as $participant) {
            $hasUnread = Message::where('conversation_id', $participant->conversation_id)
                ->where('id', '>', $participant->last_read_message_id ?? 0)
                ->where('sender_user_id', '!=', $userId)
                ->whereNull('deleted_at')
                ->exists();
            if ($hasUnread) $count++;
        }

        return response()->json(['count' => $count]);
    }

    /** GET /users/search — search users for new message modal */
    public function searchUsers(Request $request): JsonResponse
    {
        $q = trim($request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $authUser = auth()->user();

        $query = User::where('id', '!=', $authUser->id)
            ->where(function ($builder) use ($q) {
                $builder->where('fname', 'like', "%{$q}%")
                        ->orWhere('lname', 'like', "%{$q}%")
                        ->orWhere('username', 'like', "%{$q}%");
            })
            ->whereNull('deleted_at');

        // Exclude admins from results unless auth user is admin
        if ($authUser->role !== 'admin') {
            $query->where('role', '!=', 'admin');
        }

        $users = $query->limit(8)->get()->map(function (User $user) {
            return [
                'id'            => $user->id,
                'fname'         => $user->fname,
                'lname'         => $user->lname,
                'username'      => $user->username,
                'role'          => $user->role,
                'profile_photo' => $user->profile_photo ? Storage::url($user->profile_photo) : null,
                'doctor_status' => $user->doctor_status,
            ];
        });

        return response()->json($users);
    }
}
