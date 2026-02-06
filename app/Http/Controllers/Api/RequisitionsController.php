<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recruitment\Requisition;

class RequisitionsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'requested_by' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'opening' => 'required|integer',
        ]);

        $requisition = Requisition::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $requisition,
        ], 201);

    }
}
