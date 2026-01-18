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
            'requisition_title' => 'required|string|max:255',
            'requisition_description' => 'nullable|string',
            'requisition_type' => 'required|string|max:100',
            'requisition_arrangement' => 'nullable|string',
            'requisition_responsibilities' => 'nullable|string',
            'requisition_qualifications' => 'nullable|string',
            'opening' => 'required|integer',
            'status' => 'required|string|max:50',
        ]);

        $requisition = Requisition::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $requisition,
        ], 201);

    }
}
