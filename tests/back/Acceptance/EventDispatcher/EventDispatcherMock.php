<?php

declare(strict_types=1);

/*
 * @copyright 2021 Akeneo SAS (https://www.akeneo.com)
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Akeneo\Test\Acceptance\EventDispatcher;

use Behat\Behat\Context\Context;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author    Nicolas Marniesse <nicolas.marniesse@akeneo.com>
 * @copyright 2020 Akeneo SAS (http://www.akeneo.com)
 */
final class EventDispatcherMock implements Context, EventDispatcherInterface
{
    private EventDispatcherInterface $eventDispatcher;
    private array $events = [];

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    public function getEventsByName(string $eventName): array
    {
        return array_map(
            fn (array $event) => $event['event'],
            array_filter(
                $this->events,
                fn (array $event): bool => $eventName === $event['name']
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($event)
    {
        $eventName = null;
        if (1 < \func_num_args()) {
            $eventName = func_get_arg(1);
            $this->eventDispatcher->dispatch($event, $eventName);
        } else {
            $this->eventDispatcher->dispatch($event);
        }

        if (\is_object($event)) {
            $eventName = $eventName ?? \get_class($event);
        } elseif (\is_string($event) && (null === $eventName || \is_object($eventName))) {
            // Deprecated since symfony 4.3
            // See https://github.com/symfony/event-dispatcher/blob/v4.3.11/EventDispatcher.php#L58
            $swap = $event;
            $event = $eventName;
            $eventName = $swap;
        }

        $this->events[] = ['name' => $eventName, 'event' => $event];
    }

    /**
     * {@inheritdoc}
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->eventDispatcher->addListener($eventName, $listener, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->eventDispatcher->addSubscriber($subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener($eventName, $listener)
    {
        $this->eventDispatcher->removeListener($eventName, $listener);
    }

    /**
     * {@inheritdoc}
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->eventDispatcher->removeSubscriber($subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners($eventName = null)
    {
        return $this->eventDispatcher->getListeners($eventName);
    }

    /**
     * {@inheritdoc}
     */
    public function getListenerPriority($eventName, $listener)
    {
        return $this->eventDispatcher->getListenerPriority($eventName, $listener);
    }

    /**
     * {@inheritdoc}
     */
    public function hasListeners($eventName = null)
    {
        return $this->eventDispatcher->hasListeners($eventName);
    }
}
