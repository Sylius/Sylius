<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Util;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\ResourceEventInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CurrencyFactoryInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Zenstruck\Foundry\Proxy;

trait FindOrCreateCurrencyTrait
{
    /**
     * @return CurrencyInterface|Proxy
     */
    private function findOrCreateCurrency(EventDispatcherInterface $eventDispatcher, array $attributes): Proxy
    {
        /** @var ResourceEventInterface $event */
        $event = $eventDispatcher->dispatch(
            new FindOrCreateResourceEvent(CurrencyFactoryInterface::class, $attributes),
        );

        return $event->getResource();
    }
}
