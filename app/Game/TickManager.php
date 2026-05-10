<?php

declare(strict_types=1);

namespace App\Game;

use App\Events\EventDispatcher;

class TickManager
{
    private EventDispatcher $dispatcher;
    private int $tickIntervalSeconds;

    public function __construct(EventDispatcher $dispatcher, int $tickIntervalSeconds = 21600)
    {
        $this->dispatcher = $dispatcher;
        $this->tickIntervalSeconds = $tickIntervalSeconds;
    }

    public function runTick(): void
    {
        $this->dispatcher->dispatch('game.tick.start');
        $this->dispatcher->dispatch('game.tick.economy');
        $this->dispatcher->dispatch('game.tick.production');
        $this->dispatcher->dispatch('game.tick.events');
        $this->dispatcher->dispatch('game.tick.reset');
        $this->dispatcher->dispatch('game.tick.end');
    }
}
