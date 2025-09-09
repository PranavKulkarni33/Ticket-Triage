<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Jobs\ClassifyTicket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $req)
    {
        $q = Ticket::query();

        if ($s = $req->query('status'))   $q->where('status', $s);
        if ($c = $req->query('category')) $q->where('category', $c);
        if ($t = $req->query('search'))   $q->where(function($qq) use ($t) {
            $qq->where('subject', 'like', "%$t%")
               ->orWhere('body', 'like', "%$t%");
        });

        return $q->orderByDesc('created_at')->paginate(10);
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);
        $ticket = Ticket::create($data);
        return response()->json($ticket, 201);
    }

    public function show(string $id)
    {
        return Ticket::findOrFail($id);
    }

    public function update(Request $req, string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $data = $req->validate([
            'status'   => 'in:open,in_progress,closed',
            'category' => 'nullable|string|max:100',
            'note'     => 'nullable|string',
        ]);
        $ticket->update($data);
        return $ticket;
    }

    public function classify(string $id)
    {
        ClassifyTicket::dispatch($id);
        return response()->json(['queued' => true]);
    }
}
