<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    public function toggleDarkMode(): JsonResponse
    {
        $user = auth()->user();

        if ($user->isDemo()) {
            return response()->json([
                'error'     => 'Not available for demo accounts.',
                'dark_mode' => false,
            ], 403);
        }

        $user->dark_mode = !$user->dark_mode;
        $user->save();

        return response()->json(['dark_mode' => (bool) $user->dark_mode]);
    }
}
