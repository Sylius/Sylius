<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\EventSubscriber;

use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateChannelByQueryStringEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FindOrCreateChannelSubscriber implements EventSubscriberInterface
{
    public function __construct(private ChannelFactoryInterface $channelFactory)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [FindOrCreateChannelByQueryStringEvent::class  => ['findOrCreateChannel', -10]];
    }

    public function findOrCreateChannel(FindOrCreateChannelByQueryStringEvent $event): void
    {
        $event->setChannel($this->channelFactory::findOrCreate(['code' => $event->getQueryString()]));

        $event->stopPropagation();
    }
}
