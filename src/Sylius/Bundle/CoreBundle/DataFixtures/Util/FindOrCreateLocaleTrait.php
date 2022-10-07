<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Util;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\ResourceEventInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CountryFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactoryInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Zenstruck\Foundry\Proxy;

trait FindOrCreateLocaleTrait
{
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @return LocaleInterface|Proxy
     */
    private function findOrCreateLocale(array $attributes): Proxy
    {
        /** @var ResourceEventInterface $event */
        $event = $this->eventDispatcher->dispatch(
            new FindOrCreateResourceEvent(LocaleFactoryInterface::class, $attributes),
        );

        return $event->getResource();
    }
}
