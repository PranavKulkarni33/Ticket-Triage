<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\TicketClassifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;



class ClassifyTicket implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public string $ticketId) {}

    public function handle(TicketClassifier $classifier): void
    {
        $ticket = Ticket::findOrFail($this->ticketId);
        $result = $classifier->classify($ticket);

        // keep manual category if user already changed it
        $newCategory = $ticket->category ?: $result['category'];

        $ticket->update([
            'category'    => $newCategory,
            'explanation' => $result['explanation'],
            'confidence'  => $result['confidence'],
        ]);
    }
}
