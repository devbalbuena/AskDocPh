<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\CommunityPoll;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GroupController extends Controller
{
    private function layout(): string
    {
        return match (auth()->user()->role) {
            'doctor' => 'layouts.doctor',
            'admin'  => 'layouts.admin',
            default  => 'layouts.patient',
        };
    }

    /** GET /communities */
    public function index(Request $request): View
    {
        $query = Group::withCount('members')
            ->with('creator')
            ->where('visibility', '!=', 'secret')
            ->whereNull('deleted_at')
            ->latest();

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $groups = $query->paginate(12);

        $myMemberships = GroupMember::where('user_id', auth()->id())
            ->where('status', 'active')
            ->pluck('group_id')
            ->toArray();

        $layout = $this->layout();
        return view('shared.communities.index', compact('groups', 'myMemberships', 'layout'));
    }

    /** GET /communities/create */
    public function create(): View
    {
        $layout = $this->layout();
        return view('shared.communities.create', compact('layout'));
    }

    /** POST /communities */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'guidelines'  => ['nullable', 'string', 'max:2000'],
            'visibility'  => ['required', 'in:public,private'],
            'cover_photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $photoPath = null;
        if ($request->hasFile('cover_photo')) {
            $photoPath = $request->file('cover_photo')->store('groups', 'public');
        }

        $group = Group::create([
            'name'        => $request->name,
            'description' => $request->description,
            'guidelines'  => $request->guidelines,
            'visibility'  => $request->visibility,
            'cover_photo' => $photoPath,
            'creator_id'  => auth()->id(),
        ]);

        // Creator becomes admin member
        GroupMember::create([
            'group_id'   => $group->id,
            'user_id'    => auth()->id(),
            'role'       => 'admin',
            'status'     => 'active',
            'invited_by' => null,
        ]);

        return redirect()->route('communities.show', $group->id)
            ->with('success', 'Community created!');
    }

    /** GET /communities/{group} */
    public function show(Group $group): View
    {
        abort_if($group->deleted_at !== null, 404);

        $membership = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        $posts = Post::where('group_id', $group->id)
            ->with(['user', 'likes', 'comments', 'media'])
            ->latest()
            ->paginate(15);

        $members = GroupMember::where('group_id', $group->id)
            ->where('status', 'active')
            ->with('user')
            ->latest()
            ->limit(10)
            ->get();

        $membersCount = GroupMember::where('group_id', $group->id)
            ->where('status', 'active')
            ->count();

        $isMember = $group->members()
            ->where('user_id', auth()->id())
            ->exists();

        $polls = CommunityPoll::where('group_id', $group->id)
            ->with(['user', 'options', 'votes'])
            ->latest()
            ->get();

        $layout = $this->layout();
        return view('shared.communities.show', compact(
            'group', 'membership', 'posts', 'members', 'membersCount', 'layout', 'isMember', 'polls'
        ));
    }

    /** POST /communities/{group}/join */
    public function join(Group $group): JsonResponse
    {
        $existing = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            if ($existing->status === 'active') {
                return response()->json(['message' => 'Already a member.'], 422);
            }
            $existing->update(['status' => 'active']);
        } else {
            GroupMember::create([
                'group_id' => $group->id,
                'user_id'  => auth()->id(),
                'role'     => 'member',
                'status'   => $group->visibility === 'public' ? 'active' : 'pending',
            ]);
        }

        return response()->json(['success' => true, 'status' => 'active']);
    }

    /** POST /communities/{group}/leave */
    public function leave(Group $group): JsonResponse
    {
        // Creator cannot leave
        abort_if($group->creator_id === auth()->id(), 422);

        GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->delete();

        return response()->json(['success' => true]);
    }

    /** POST /communities/{group}/post — create a post in the group */
    public function createPost(Request $request, Group $group): JsonResponse
    {
        // Must be active member
        $isMember = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->exists();

        abort_unless($isMember, 403, 'You must be a member to post in this community.');

        $request->validate([
            'text_content' => ['required', 'string', 'max:5000'],
        ]);

        $post = Post::create([
            'user_id'      => auth()->id(),
            'group_id'     => $group->id,
            'post_type'    => 'text',
            'text_content' => $request->text_content,
        ]);

        $post->load('user');

        return response()->json([
            'success' => true,
            'post'    => [
                'id'           => $post->id,
                'text_content' => $post->text_content,
                'author'       => $post->user->display_name,
                'created_at'   => $post->created_at->diffForHumans(),
            ],
        ]);
    }
}
