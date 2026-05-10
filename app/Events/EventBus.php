<?php

declare(strict_types=1);

namespace App\Events;

class EventBus
{
    private array $listeners = [];

    public function subscribe(string $eventName, callable $listener): void
    {
        $this->listeners[$eventName][] = $listener;
    }

    public function publish(string $eventName, array $payload = []): void
    {
        foreach ($this->listeners[$eventName] ?? [] as $listener) {
            $listener($payload);
        }
    }
}
