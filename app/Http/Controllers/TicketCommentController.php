<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketCommentController extends Controller
{
    public function store(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'comment' => ['required', 'string'],
            'is_internal' => ['nullable', 'boolean'],
        ]);

        $ticket->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $validated['comment'],
            'is_internal' => $request->boolean('is_internal'),
        ]);

        if (in_array($ticket->status, ['open', 'pending'], true)) {
            $ticket->update(['status' => 'in_progress']);
        }

        return redirect()->route('tickets.show', $ticket)->with('success', 'Comment added.');
    }
}
