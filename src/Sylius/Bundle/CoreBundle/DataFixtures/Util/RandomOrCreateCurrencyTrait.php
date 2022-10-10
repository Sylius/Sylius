<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Util;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\RandomOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\ResourceEventInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CurrencyFactoryInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Zenstruck\Foundry\Proxy;

trait RandomOrCreateCurrencyTrait
{
    /**
     * @return CurrencyInterface|Proxy
     */
    private function randomOrCreateCurrency(EventDispatcherInterface $eventDispatcher): Proxy
    {
        /** @var ResourceEventInterface $event */
        $event = $eventDispatcher->dispatch(
            new RandomOrCreateResourceEvent(CurrencyFactoryInterface::class)
        );

        return $event->getResource();
    }
}
