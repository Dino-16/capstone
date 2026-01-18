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
}
