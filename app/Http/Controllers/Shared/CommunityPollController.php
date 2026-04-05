<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\CommunityPoll;
use App\Models\CommunityPollVote;
use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommunityPollController extends Controller
{
    /** POST /communities/{group}/polls */
    public function store(Request $request, Group $group): JsonResponse
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'options'  => 'required|array|min:2|max:6',
            'options.*'=> 'required|string|max:100',
            'ends_at'  => 'nullable|date|after:now',
        ]);

        $poll = CommunityPoll::create([
            'user_id'  => auth()->id(),
            'group_id' => $group->id,
            'question' => $request->question,
            'ends_at'  => $request->ends_at,
        ]);

        foreach ($request->options as $text) {
            $poll->options()->create(['text' => $text]);
        }

        return response()->json(['success' => true, 'poll_id' => $poll->id]);
    }

    /** POST /communities/{group}/polls/{poll}/vote */
    public function communityVote(Request $request, Group $group, CommunityPoll $poll): JsonResponse
    {
        $request->validate([
            'option_id' => ['required', 'exists:community_poll_options,id'],
        ]);

        if ($poll->hasVoted(auth()->id())) {
            return response()->json(['message' => 'You have already voted on this poll.'], 422);
        }

        if ($poll->ends_at && $poll->ends_at->isPast()) {
            return response()->json(['message' => 'This poll has ended.'], 422);
        }

        if (!$poll->options()->where('id', $request->option_id)->exists()) {
            return response()->json(['message' => 'Invalid option for this poll.'], 422);
        }

        CommunityPollVote::create([
            'poll_id'   => $poll->id,
            'option_id' => $request->option_id,
            'user_id'   => auth()->id(),
        ]);

        return response()->json(['success' => true]);
    }

    /** POST /polls/{poll}/vote (global feed votes) */
    public function vote(Request $request, CommunityPoll $poll): JsonResponse
    {
        $request->validate([
            'option_id' => ['required', 'exists:community_poll_options,id'],
        ]);

        if ($poll->hasVoted(auth()->id())) {
            return response()->json(['message' => 'You have already voted on this poll.'], 422);
        }

        if ($poll->ends_at && $poll->ends_at->isPast()) {
            return response()->json(['message' => 'This poll has ended.'], 422);
        }

        if (!$poll->options()->where('id', $request->option_id)->exists()) {
            return response()->json(['message' => 'Invalid option for this poll.'], 422);
        }

        CommunityPollVote::create([
            'poll_id'   => $poll->id,
            'option_id' => $request->option_id,
            'user_id'   => auth()->id(),
        ]);

        return response()->json(['success' => true]);
    }
}
