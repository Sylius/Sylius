<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Util;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\RandomOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\ResourceEventInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerGroupFactoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Zenstruck\Foundry\Proxy;

trait RandomOrCreateCustomerGroupTrait
{
    /**
     * @return CustomerGroupInterface|Proxy
     */
    private function randomOrCreateCustomerGroup(EventDispatcherInterface $eventDispatcher): Proxy
    {
        /** @var ResourceEventInterface $event */
        $event = $eventDispatcher->dispatch(
            new RandomOrCreateResourceEvent(CustomerGroupFactoryInterface::class)
        );

        return $event->getResource();
    }
}
