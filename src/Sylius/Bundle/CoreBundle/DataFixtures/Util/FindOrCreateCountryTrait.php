<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Util;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\ResourceEventInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CountryFactoryInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Zenstruck\Foundry\Proxy;

trait FindOrCreateCountryTrait
{
    /**
     * @return CountryInterface|Proxy
     */
    private function findOrCreateCountry(EventDispatcherInterface $eventDispatcher, array $attributes): Proxy
    {
        /** @var ResourceEventInterface $event */
        $event = $eventDispatcher->dispatch(
            new FindOrCreateResourceEvent(CountryFactoryInterface::class, $attributes),
        );

        return $event->getResource();
    }
}
