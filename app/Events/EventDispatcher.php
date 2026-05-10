<?php

declare(strict_types=1);

namespace App\Events;

class EventDispatcher
{
    private EventBus $bus;

    public function __construct(EventBus $bus)
    {
        $this->bus = $bus;
    }

    public function dispatch(string $eventName, array $payload = []): void
    {
        $this->bus->publish($eventName, $payload);
    }
}
