<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RentalController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $this->requirePermission('bookings.view');

        $rentals = Booking::query()
            ->with(['property', 'customer'])
            ->whereIn('status', ['confirmed', 'active', 'completed'])
            ->when($request->q, fn ($q, $term) => $q->where('code', 'like', '%'.$term.'%'))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest('start_date')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Rentals/Index', [
            'rentals' => $rentals,
            'filters' => $request->only(['q', 'status']),
        ]);
    }
}
