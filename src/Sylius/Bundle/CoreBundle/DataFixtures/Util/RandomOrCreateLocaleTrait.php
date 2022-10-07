<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Util;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\RandomOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\ResourceEventInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CountryFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactoryInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Zenstruck\Foundry\Proxy;

trait RandomOrCreateLocaleTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function randomOrCreateLocale(): Proxy|LocaleInterface
    {
        /** @var ResourceEventInterface $event */
        $event = $this->eventDispatcher->dispatch(
            new RandomOrCreateResourceEvent(LocaleFactoryInterface::class),
        );

        /** @var LocaleInterface $locale */
        $locale = $event->getResource();

        return $locale;
    }
}
