<?php

namespace App\Http\Controllers\Web\Documents;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShtatkaController extends Controller
{
    public function index(Request $request): Response
    {
        // Get per page value (default: 20)
        $perPage = $request->input('per_page', 20);

        // Build query with relations
        $query = Branch::with(['state', 'region']);

        // Apply search
        if ($request->filled('search')) {
            $query = $query->search($request->search);
        }

        // Apply sorting
        $sortKey = $request->input('sort', 'id');
        $sortDirection = $request->input('direction', 'asc');

        if (in_array($sortKey, ['id', 'name', 'created_at'])) {
            $query = $query->orderBy($sortKey, $sortDirection);
        }

        // Paginate
        $branches = $query->paginate($perPage)->withQueryString();

        // Transform data similar to BranchResource
        $branches->getCollection()->transform(function ($branch) {
            // Format phone number to +(998) XX XXX XX XX
            $formattedPhone = $branch->phone_number;
            if ($formattedPhone) {
                // Remove all non-digit characters
                $digits = preg_replace('/\D/', '', $formattedPhone);

                // Format to +(998) XX XXX XX XX
                if (strlen($digits) >= 9) {
                    $formattedPhone = sprintf(
                        '+(998) %s %s %s %s',
                        substr($digits, -9, 2),
                        substr($digits, -7, 3),
                        substr($digits, -4, 2),
                        substr($digits, -2, 2)
                    );
                }
            }

            return [
                'id' => $branch->id,
                'name' => $branch->name,
                'code' => $branch->code,
                'state_id' => $branch->state_id,
                'state_name' => $branch->state->name ?? null,
                'region_id' => $branch->region_id,
                'region_name' => $branch->region->name ?? null,
                'address' => $branch->address,
                'phone_number' => $formattedPhone,
                'target' => $branch->target,
                'location' => $branch->location,
                'responsible_worker' => $branch->responsible_worker,
                'date' => date_format($branch->created_at, 'd.m.Y H:i'),
            ];
        });

        return Inertia::render('Documents/shtatka', [
            'branches' => $branches,
            'filters' => $request->only(['search']),
        ]);
    }
        public function indexTest(Request $request): Response
    {
        // Get per page value (default: 20)
        $perPage = $request->input('per_page', 20);

        // Build query with filters
        $query = Branch::withTrashed();

        // Apply ModelFilter
        if ($request->hasAny(['id', 'name', 'address', 'phone_number', 'target'])) {
            $query = $query->filter($request->all());
        }

        // Apply search
        if ($request->filled('search')) {
            $query = $query->search($request->search);
        }

        // Apply sorting
        $sortKey = $request->input('sort', 'id');
        $sortDirection = $request->input('direction', 'asc');

        if (in_array($sortKey, ['id', 'name', 'created_at'])) {
            $query = $query->orderBy($sortKey, $sortDirection);
        }

        // Paginate
        $branches = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Documents/branch', [
            'branches' => $branches,
            'filters' => $request->only(['search']),
        ]);
    }

}
