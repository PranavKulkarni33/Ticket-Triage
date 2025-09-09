<?php

namespace Database\Factories;

use App\Models\Ticket; 
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;
    
    public function definition(): array
    {
        $subjects = [
            'Billing issue with invoice', 'Cannot login to portal',
            'Feature request: dark mode', 'Refund not processed',
            'Email not sending', 'App crash on submit'
        ];
        $bodies = [
            'Customer reports a charge twice on VISA ending 1234.',
            'User sees 500 error at /auth/callback.',
            'Wants a quick toggle for dark theme.',
            'Refund promised last week not received.',
            'SMTP creds valid but emails stuck in queue.',
            'Clicking submit causes a white screen.'
        ];
        $maybeNote = fake()->boolean(35) ? 'Internal: follow up with Ops.' : null;

        return [
            'subject' => fake()->randomElement($subjects),
            'body' => fake()->randomElement($bodies).' '.fake()->sentence(10),
            'status' => fake()->randomElement(['open','in_progress','closed']),
            'category' => null,
            'note' => $maybeNote,
            'confidence' => null,
            'explanation' => null,
        ];
    }
}
