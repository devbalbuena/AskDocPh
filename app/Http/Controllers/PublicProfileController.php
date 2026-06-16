<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DoctorReview;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicProfileController extends Controller
{
    /** Resolve the correct layout for the authenticated user's role. */
    private function layout(): string
    {
        return match(auth()->user()->role) {
            'doctor' => 'layouts.doctor',
            'admin'  => 'layouts.admin',
            default  => 'layouts.patient',
        };
    }

    /**
     * GET /users/{username}
     * Show a public profile page for the given username.
     */
    public function show(string $username): View
    {
        // Find user by username or 404
        $profileUser = User::where('username', $username)->firstOrFail();

        // Load posts with their media, likes, and comment counts
        $posts = $profileUser->posts()
            ->with(['media', 'likes', 'comments'])
            ->latest()
            ->paginate(12);

        // Check if the authenticated user is following this profile
        $isFollowing = auth()->user()->isFollowing($profileUser);

        // Follower / following counts
        $followersCount = $profileUser->followersCount();
        $followingCount = $profileUser->followingCount();

        // Doctor-specific: load extra doctor info
        $professionalTitles = [];
        $doctorReviews = collect();
        $doctorAverageRating = 0;

        if ($profileUser->role === 'doctor') {
            $application = $profileUser->doctorApplications()
                ->with('professionalTitles')
                ->latest()
                ->first();

            if ($application) {
                $professionalTitles = $application->professionalTitles->pluck('title')->toArray();
            }

            $doctorReviews = DoctorReview::where('doctor_id', $profileUser->id)
                ->with('patient')
                ->latest()
                ->get();
                
            $doctorAverageRating = $doctorReviews->avg('rating') ?? 0;
        }

        return view('shared.profile.public', [
            'profileUser'        => $profileUser,
            'posts'              => $posts,
            'isFollowing'        => $isFollowing,
            'followersCount'     => $followersCount,
            'followingCount'     => $followingCount,
            'professionalTitles' => $professionalTitles,
            'doctorReviews'      => $doctorReviews,
            'doctorAverageRating'=> $doctorAverageRating,
            'layout'             => $this->layout(),
            'isOwnProfile'       => auth()->id() === $profileUser->id,
        ]);
    }
}
