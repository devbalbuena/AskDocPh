<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyAffirmation;
use Illuminate\Http\JsonResponse;
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

    /** POST /admin/affirmations — create new (Axios JSON) */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'quote'        => ['required', 'string', 'max:1000'],
            'author'       => ['nullable', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
            'publish_at'   => ['nullable', 'date'],
        ]);

        $affirmation = DailyAffirmation::create([
            'quote'               => $request->quote,
            'author'              => $request->author,
            'is_published'        => $request->boolean('is_published'),
            'publish_at'          => $request->publish_at ?? now(),
            'created_by_admin_id' => null, // users table != admins table; left null until admin auth is built
        ]);

        return response()->json([
            'success'     => true,
            'affirmation' => [
                'id'           => $affirmation->id,
                'quote'        => $affirmation->quote,
                'author'       => $affirmation->author,
                'is_published' => $affirmation->is_published,
                'publish_at'   => $affirmation->publish_at?->format('M d, Y'),
            ],
        ]);
    }

    /** PUT /admin/affirmations/{affirmation} — edit (Axios JSON) */
    public function update(Request $request, DailyAffirmation $affirmation): JsonResponse
    {
        $request->validate([
            'quote'        => ['required', 'string', 'max:1000'],
            'author'       => ['nullable', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
            'publish_at'   => ['nullable', 'date'],
        ]);

        $affirmation->update([
            'quote'        => $request->quote,
            'author'       => $request->author,
            'is_published' => $request->boolean('is_published'),
            'publish_at'   => $request->publish_at,
        ]);

        return response()->json(['success' => true]);
    }

    /** DELETE /admin/affirmations/{affirmation} — delete (Axios JSON) */
    public function destroy(DailyAffirmation $affirmation): JsonResponse
    {
        $affirmation->delete();

        return response()->json(['success' => true]);
    }
}
