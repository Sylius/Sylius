<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\EventSubscriber;

use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateChannelByCodeEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateLocaleByCodeEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FindOrCreateLocaleByCodeSubscriber implements EventSubscriberInterface
{
    public function __construct(private LocaleFactoryInterface $localeFactory)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [FindOrCreateLocaleByCodeEvent::class  => ['findOrCreateLocale', -10]];
    }

    public function findOrCreateLocale(FindOrCreateLocaleByCodeEvent $event): void
    {
        $event->setLocale($this->localeFactory::findOrCreate(['code' => $event->getCode()]));
    }
}
