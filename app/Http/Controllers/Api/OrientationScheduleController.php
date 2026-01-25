<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Onboarding\Orientation;

class OrientationScheduleController extends Controller
{
    public function index()
    {
        return Orientation::latest()->get();
    }

    public function updateStatus(Request $request, $id)
    {
        // 1. Validate that 'status' is provided and is a string
        $validated = $request->validate([
            'status' => 'required|string|max:255', 
        ]);

        // 2. Find the record
        $schedule = Orientation::findOrFail($id);

        // 3. Update ONLY the status
        $schedule->status = $validated['status'];
        $schedule->save();

        return response()->json([
            'message' => 'Status updated successfully',
            'new_status' => $schedule->status
        ]);
    }
}
