<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AIChatController extends Controller
{
    public function respond(Request $request)
    {
        // Demo account check rule as specified
        if (auth()->check() && auth()->user()->isDemo()) {
            // Demo accounts bypass any hypothetical limit/restrictions here.
        }

        $request->validate([
            'message' => 'required|string|max:500'
        ]);

        $message = strtolower($request->message);

        $response = "Thank you for sharing. For personalized mental health support, I recommend speaking with one of our verified doctors. Would you like to find one?";
        $showFindDoctor = true; // For default response
        $showCrisis = false;

        if (str_contains($message, 'suicide') || str_contains($message, 'self harm')) {
            $response = "I am very concerned about what you shared. Please reach out for immediate help. Use the Get Help Now button on your dashboard to connect with a doctor right away. You matter and help is available.";
            $showCrisis = true;
            $showFindDoctor = false;
        } elseif (str_contains($message, 'anxiety')) {
            $response = "Anxiety is very common. Try deep breathing exercises: inhale for 4 counts, hold for 4, exhale for 4. Consider booking a session with one of our verified doctors.";
            $showFindDoctor = true;
        } elseif (str_contains($message, 'depression')) {
            $response = "Depression is a serious condition. You are not alone. Our verified doctors are here to help. Would you like to find a doctor?";
            $showFindDoctor = true;
        } elseif (str_contains($message, 'stress')) {
            $response = "Stress management starts with small steps. Try taking breaks, exercise, and talking to someone you trust. Our community feed is also a great place to share your feelings.";
            $showFindDoctor = false;
        } elseif (str_contains($message, 'sleep')) {
            $response = "Sleep problems are often linked to stress and anxiety. Try maintaining a regular sleep schedule and limiting screen time before bed.";
            $showFindDoctor = false;
        }

        return response()->json([
            'response' => $response,
            'show_find_doctor' => $showFindDoctor,
            'show_crisis' => $showCrisis
        ]);
    }
}
