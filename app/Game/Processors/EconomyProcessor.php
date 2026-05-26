<?php

declare(strict_types=1);

namespace App\Game\Processors;

use App\Events\EventDispatcher;

class EconomyProcessor
{
    private EventDispatcher $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function process(): void
    {
        // Future business logic will calculate stock prices, demand, supply, and passive income.
        $this->dispatcher->dispatch('economy.updated', []);
    }
}
