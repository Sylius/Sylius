<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\EventSubscriber;

use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateZoneMemberByQueryStringEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneMemberFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FindOrCreateZoneMemberSubscriber implements EventSubscriberInterface
{
    public function __construct(private ZoneMemberFactoryInterface $zoneMemberFactory)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [FindOrCreateZoneMemberByQueryStringEvent::class  => ['findOrCreateZoneMember', -10]];
    }

    public function findOrCreateZoneMember(FindOrCreateZoneMemberByQueryStringEvent $event): void
    {
        $event->setZoneMember($this->zoneMemberFactory::findOrCreate(['code' => $event->getQueryString()]));

        $event->stopPropagation();
    }
}
