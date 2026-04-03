<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\MoodEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class MoodTrackerController extends Controller
{
    /** Map mood numeric score to string for DB */
    private function scoreToMood(int $score): string
    {
        return match ($score) {
            5 => 'happy',
            4 => 'calm',
            3 => 'neutral',
            2 => 'sad',
            1 => 'angry',
            default => 'neutral',
        };
    }

    /** Map mood string to numeric score for charting */
    private function moodScore(string $mood): int
    {
        return match ($mood) {
            'happy'   => 5,
            'calm'    => 4,
            'neutral' => 3,
            'sad'     => 2,
            'anxious' => 2,
            'angry'   => 1,
            default   => 3,
        };
    }

    /** GET /patient/mood */
    public function index(): View
    {
        $userId = auth()->id();

        // Get past 14 days entries (including today)
        $date14DaysAgo = now()->subDays(13)->startOfDay();
        
        $entriesQuery = MoodEntry::where('user_id', $userId)
            ->where('entry_date', '>=', $date14DaysAgo->toDateString())
            ->orderByDesc('entry_date')
            ->get();

        // For the table, show recent entries directly mapping score
        $entries = clone $entriesQuery;
        
        // Add dynamic property mood_score to entries
        $entries->map(function ($entry) {
            $entry->mood_score = $this->moodScore($entry->mood);
            return $entry;
        });

        // Build chart data (last 14 days, chronological)
        $chartLabels = [];
        $chartData = [];
        
        // Only show chart if there is at least one entry
        if ($entries->count() > 0) {
            for ($i = 13; $i >= 0; $i--) {
                $dayCarbon = now()->subDays($i);
                $dayStr    = $dayCarbon->toDateString();
                
                // Find entry for this day
                $entry = $entriesQuery->first(function ($e) use ($dayStr) {
                    $entryDateStr = $e->entry_date instanceof \Carbon\Carbon 
                                  ? $e->entry_date->toDateString() 
                                  : Carbon::parse($e->entry_date)->toDateString();
                    return $entryDateStr === $dayStr;
                });
                
                if ($entry) {
                    $chartLabels[] = $dayCarbon->format('M d');
                    $chartData[] = $this->moodScore($entry->mood);
                }
            }
        }

        return view('patient.mood.index', compact('entries', 'chartLabels', 'chartData'));
    }

    /** POST /patient/mood — log or update a mood */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'mood_score' => ['required', 'integer', 'between:1,5'],
            'notes'      => ['nullable', 'string', 'max:1000'],
            'entry_date' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $moodString = $this->scoreToMood((int) $request->mood_score);
        $entryDate = Carbon::parse($request->entry_date)->toDateString();

        MoodEntry::updateOrCreate(
            ['user_id' => auth()->id(), 'entry_date' => $entryDate],
            ['mood' => $moodString, 'notes' => $request->notes]
        );

        return back()->with('success', 'Mood entry saved successfully.');
    }

    /** GET /patient/mood/history — for doctor to view a specific patient's history */
    public function history(Request $request): JsonResponse
    {
        $userId = $request->query('patient_id', auth()->id());

        // If not own history, must be a doctor
        if ($userId != auth()->id()) {
            abort_unless(auth()->user()->role === 'doctor', 403);
        }

        $entries = MoodEntry::where('user_id', $userId)
            ->where('entry_date', '>=', now()->subDays(29)->toDateString())
            ->orderBy('entry_date')
            ->get()
            ->map(fn ($e) => [
                'date'  => $e->entry_date instanceof \Carbon\Carbon ? $e->entry_date->toDateString() : $e->entry_date,
                'mood'  => $e->mood,
                'notes' => $e->notes,
                'score' => $this->moodScore($e->mood),
            ]);

        return response()->json($entries);
    }
}
