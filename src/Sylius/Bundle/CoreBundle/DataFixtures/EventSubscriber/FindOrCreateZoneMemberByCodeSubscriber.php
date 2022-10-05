<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\EventSubscriber;

use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateZoneMemberByCodeEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneMemberFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FindOrCreateZoneMemberByCodeSubscriber implements EventSubscriberInterface
{
    public function __construct(private ZoneMemberFactoryInterface $zoneMemberFactory)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [FindOrCreateZoneMemberByCodeEvent::class  => ['findOrCreateZoneMember', -10]];
    }

    public function findOrCreateZoneMember(FindOrCreateZoneMemberByCodeEvent $event): void
    {
        $event->setZoneMember($this->zoneMemberFactory::findOrCreate(['code' => $event->getCode()]));
    }
}
