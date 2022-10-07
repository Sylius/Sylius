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
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @return CountryInterface|Proxy
     */
    private function findOrCreateCountry(array $attributes): Proxy
    {
        /** @var ResourceEventInterface $event */
        $event = $this->eventDispatcher->dispatch(
            new FindOrCreateResourceEvent(CountryFactoryInterface::class, $attributes),
        );

        return $event->getResource();
    }
}
