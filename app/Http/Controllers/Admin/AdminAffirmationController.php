<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyAffirmation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAffirmationController extends Controller
{
    /** GET /admin/affirmations — list all affirmations */
    public function index(): View
    {
        $affirmations = DailyAffirmation::latest()->paginate(20);

        return view('admin.affirmations.index', compact('affirmations'));
    }

    /** POST /admin/affirmations — create new affirmation */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'quote'        => ['required', 'string', 'max:1000'],
            'author'       => ['nullable', 'string', 'max:255'],
            'is_published' => ['boolean'],
            'publish_at'   => ['nullable', 'date'],
        ]);

        DailyAffirmation::create([
            'quote'               => $request->quote,
            'author'              => $request->author,
            'is_published'        => $request->boolean('is_published'),
            'publish_at'          => $request->publish_at ?? now(),
            'created_by_admin_id' => auth()->id(),
        ]);

        return redirect()->route('admin.affirmations.index')
            ->with('success', 'Affirmation created.');
    }

    /** PUT /admin/affirmations/{affirmation} — edit */
    public function update(Request $request, DailyAffirmation $affirmation): RedirectResponse
    {
        $request->validate([
            'quote'        => ['required', 'string', 'max:1000'],
            'author'       => ['nullable', 'string', 'max:255'],
            'is_published' => ['boolean'],
            'publish_at'   => ['nullable', 'date'],
        ]);

        $affirmation->update($request->only(['quote', 'author', 'is_published', 'publish_at']));

        return redirect()->route('admin.affirmations.index')
            ->with('success', 'Affirmation updated.');
    }

    /** DELETE /admin/affirmations/{affirmation} — delete */
    public function destroy(DailyAffirmation $affirmation): RedirectResponse
    {
        $affirmation->delete();

        return redirect()->route('admin.affirmations.index')
            ->with('success', 'Affirmation deleted.');
    }
}
