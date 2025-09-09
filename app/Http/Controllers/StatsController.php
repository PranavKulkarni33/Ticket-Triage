<?php

namespace App\Http\Controllers;

use App\Models\Ticket;

class StatsController extends Controller
{
    public function index()
    {
        // Simple counts (no tricky SQL)
        $status = [
            'open'        => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'closed'      => Ticket::where('status', 'closed')->count(),
        ];

        // Group in PHP to avoid SQL edge cases
        $byCategory = Ticket::whereNotNull('category')->get()
            ->groupBy('category')
            ->map(fn ($group) => $group->count());

        return [
            'status'   => $status,
            'category' => $byCategory, // e.g. {"billing": 4, "technical": 6}
        ];
    }
}
