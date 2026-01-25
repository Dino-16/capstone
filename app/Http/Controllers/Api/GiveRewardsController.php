<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recognition\GiveReward;

class GiveRewardsController extends Controller
{
    public function index()
    {
        return GiveReward::with('reward')->latest()->get(); // new
    }
    public function updateStatus(Request $request, $id)
    {
        // Validate that the status is sent and is a string
        $request->validate([
            'status' => 'required|string'
        ]);

        // Find the specific reward record
        $giveReward = GiveReward::findOrFail($id);

        // Update only the status
        $giveReward->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Reward status updated!',
            'data' => $giveReward->load('reward') // Return updated data with the relationship
        ]);
    }
}
