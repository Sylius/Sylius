<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\EventSubscriber;

use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateZoneByQueryStringEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FindOrCreateZoneSubscriber implements EventSubscriberInterface
{
    public function __construct(private ZoneFactoryInterface $zoneFactory)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [FindOrCreateZoneByQueryStringEvent::class  => ['findOrCreateZone', -10]];
    }

    public function findOrCreateZone(FindOrCreateZoneByQueryStringEvent $event): void
    {
        $event->setZone($this->zoneFactory::findOrCreate(['code' => $event->getQueryString()]));

        $event->stopPropagation();
    }
}
