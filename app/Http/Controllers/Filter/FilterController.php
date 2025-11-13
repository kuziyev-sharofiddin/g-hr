<?php

namespace App\Http\Controllers\Filter;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Position;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function filterBranch()
    {
        return response()->json(
            Branch::select('id', 'name')->get(),
            200,
            ['Content-Type' => 'application/json']
        );
    }

    public function filterPosition()
    {
        return response()->json(
            Position::select('id', 'name')->get(),
            200,
            ['Content-Type' => 'application/json']
        );
    }
}
