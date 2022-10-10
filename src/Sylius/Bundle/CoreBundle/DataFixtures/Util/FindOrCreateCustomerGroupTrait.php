<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Util;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\ResourceEventInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerGroupFactoryInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Zenstruck\Foundry\Proxy;

trait FindOrCreateCustomerGroupTrait
{
    /**
     * @return CustomerGroupInterface|Proxy
     */
    private function findOrCreateCustomerGroup(EventDispatcherInterface $eventDispatcher, array $attributes): Proxy
    {
        /** @var ResourceEventInterface $event */
        $event = $eventDispatcher->dispatch(
            new FindOrCreateResourceEvent(CustomerGroupFactoryInterface::class, $attributes)
        );

        return $event->getResource();
    }
}
