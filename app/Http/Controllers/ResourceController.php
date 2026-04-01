<?php

namespace App\Http\Controllers;

use App\Models\Resource as HealthResource;
use App\Models\ResourceBody;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ResourceController extends Controller
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

    /** GET /resources — paginated resource listing, filterable by type */
    public function index(Request $request): View
    {
        $type = $request->query('type');

        $resources = HealthResource::with('author')
            ->when($type, fn($q) => $q->where('type', $type))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('shared.resources.index', [
            'resources' => $resources,
            'layout'    => $this->layout(),
            'activeType' => $type,
        ]);
    }

    /** GET /resources/{resource} — single resource detail */
    public function show(HealthResource $resource): View
    {
        $resource->load(['author', 'body']);

        return view('shared.resources.show', [
            'resource' => $resource,
            'layout'   => $this->layout(),
        ]);
    }

    /** GET /doctor/resources/create — create form (doctor only) */
    public function create(): View
    {
        return view('doctor.resources.create');
    }

    /** POST /doctor/resources — store a new resource (doctor only) */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'type'        => ['required', 'in:article,video,pdf'],
            'file'        => ['nullable', 'file', 'max:20480'],
            'content'     => ['nullable', 'string'],
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('resources', 'public');
        }

        $resource = HealthResource::create([
            'user_id'     => auth()->id(),
            'title'       => $request->title,
            'description' => $request->description,
            'type'        => $request->type,
            'file_path'   => $filePath,
        ]);

        ResourceBody::create([
            'resource_id' => $resource->id,
            'content'     => $request->content ?? '',
        ]);

        return redirect()->route('resources.index')
            ->with('success', 'Resource published successfully!');
    }

    /** DELETE /doctor/resources/{resource} — soft delete (own resources only) */
    public function destroy(HealthResource $resource): RedirectResponse
    {
        abort_unless(auth()->id() === $resource->user_id, 403);

        $resource->delete();

        return redirect()->route('resources.index')
            ->with('success', 'Resource deleted.');
    }
}
