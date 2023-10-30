<?php

namespace Eniams\SafeMigrationsBundle\Tests\App\src\EventListener;

use Eniams\SafeMigrationsBundle\Event\UnsafeMigrationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UnsafeMigrationListener implements EventSubscriberInterface
{
    /**
     * @var UnsafeMigrationEvent[]
     */
    public static array $inMemoryEvents = [];

    public static function getSubscribedEvents(): array
    {
        return [
            UnsafeMigrationEvent::class => 'onUnsafeMigration',
        ];
    }

    public function onUnsafeMigration(UnsafeMigrationEvent $event): void
    {
        self::$inMemoryEvents[] = $event;
    }
}
