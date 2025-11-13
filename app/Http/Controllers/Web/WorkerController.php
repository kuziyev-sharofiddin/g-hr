<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Worker;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkerController extends Controller
{
    public function workerHrA(Request $request)
    {
        $query = Worker::query()
            ->with(['branch', 'position']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('jshr_number', 'like', "%{$search}%")
                    ->orWhereHas('branch', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('position', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // Branch filter
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->get('branch_id'));
        }

        // Position filter
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->get('position_id'));
        }

        // Sorting
        $sortBy = $request->get('sort', 'id');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $direction);

        $workers = $query->paginate(18);

        return Inertia::render('workers/worker_hr_a', [
            'workers' => $workers,
            'filters' => [
                'search' => $request->get('search'),
                'branch_id' => $request->get('branch_id'),
                'position_id' => $request->get('position_id'),
            ],
        ]);
    }
}
