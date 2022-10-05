<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\EventSubscriber;

use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateChannelByCodeEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FindOrCreateChannelByCodeSubscriber implements EventSubscriberInterface
{
    public function __construct(private ChannelFactoryInterface $channelFactory)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [FindOrCreateChannelByCodeEvent::class  => ['findOrCreateChannel', -10]];
    }

    public function findOrCreateChannel(FindOrCreateChannelByCodeEvent $event): void
    {
        $event->setChannel($this->channelFactory::findOrCreate(['code' => $event->getCode()]));
    }
}
