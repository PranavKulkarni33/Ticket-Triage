<?php

use Illuminate\Support\Facades\Artisan;
use App\Models\Ticket;
use App\Jobs\ClassifyTicket;

Artisan::command('tickets:bulk-classify {--status=open} {--limit=50}', function () {
    $status = (string) $this->option('status');
    $limit  = (int) $this->option('limit');

    $q = Ticket::query();
    if ($status !== '') {
        $q->where('status', $status);   // open | in_progress | closed
    }

    $ids = $q->orderByDesc('created_at')->limit($limit)->pluck('id');

    $this->info("Dispatching classify jobs for {$ids->count()} tickets…");
    foreach ($ids as $id) {
        ClassifyTicket::dispatch($id);
        usleep(200000); // 0.2s pacing (~5/sec) to be gentle + respect rate limiting
    }
    $this->info('Done.');
})->purpose('Dispatch queued classification for many tickets at once');
