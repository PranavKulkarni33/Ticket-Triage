<?php

namespace App\Services;

use App\Models\Ticket;

class TicketClassifier
{
    /**
     * Dummy classifier – returns random category
     */
    public function classify(Ticket $ticket): array
    {
        $cats = ['billing', 'technical', 'account', 'general'];

        return [
            'category'    => $cats[array_rand($cats)],
            'explanation' => 'Dummy classification (OPENAI_CLASSIFY_ENABLED=false).',
            'confidence'  => round(mt_rand(50, 95) / 100, 2),
        ];
    }
}
