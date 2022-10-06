<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\EventSubscriber;

use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateChannelByQueryStringEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateLocaleByQueryStringEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FindOrCreateLocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(private LocaleFactoryInterface $localeFactory)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [FindOrCreateLocaleByQueryStringEvent::class  => ['findOrCreateLocale', -10]];
    }

    public function findOrCreateLocale(FindOrCreateLocaleByQueryStringEvent $event): void
    {
        $event->setLocale($this->localeFactory::findOrCreate(['code' => $event->getQueryString()]));

        $event->stopPropagation();
    }
}
