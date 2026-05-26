<?php

declare(strict_types=1);

namespace App\Game\Processors;

use App\Events\EventDispatcher;

class DailyResetProcessor
{
    private EventDispatcher $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function reset(): void
    {
        // Placeholder for daily game tick resets, timed events, and periodic state maintenance.
        $this->dispatcher->dispatch('game.reset.completed', []);
    }
}
