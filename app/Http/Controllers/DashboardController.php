<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        $ticketStatusTotals = Ticket::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $dashboardStats = [
            'clients_total' => Client::count(),
            'clients_active' => Client::where('status', 'active')->count(),
            'tickets_total' => Ticket::count(),
            'tickets_open' => Ticket::whereIn('status', ['open', 'in_progress', 'pending'])->count(),
            'tickets_resolved_today' => Ticket::whereDate('resolved_at', now()->toDateString())->count(),
            'tickets_unassigned' => Ticket::whereNull('assigned_to')->whereIn('status', ['open', 'in_progress', 'pending'])->count(),
        ];

        $myAssignedOpen = Ticket::query()
            ->where('assigned_to', Auth::id())
            ->whereIn('status', ['open', 'in_progress', 'pending'])
            ->count();

        $recentTickets = Ticket::query()
            ->with(['client:id,full_name', 'assignedTo:id,name'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard', [
            'dashboardStats' => $dashboardStats,
            'ticketStatusTotals' => $ticketStatusTotals,
            'myAssignedOpen' => $myAssignedOpen,
            'recentTickets' => $recentTickets,
        ]);
    }
}
