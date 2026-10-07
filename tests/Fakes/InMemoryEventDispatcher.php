<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Application\Contracts\EventDispatcherInterface;

/**
 * Hand-written in-memory double for EventDispatcherInterface. Records every
 * dispatched event so Application layer tests can assert on it without
 * touching Laravel's real event bus.
 */
final class InMemoryEventDispatcher implements EventDispatcherInterface
{
    /** @var list<object> */
    private array $dispatched = [];

    public function dispatch(object $event): void
    {
        $this->dispatched[] = $event;
    }

    /**
     * @return list<object>
     */
    public function dispatched(): array
    {
        return $this->dispatched;
    }
}
