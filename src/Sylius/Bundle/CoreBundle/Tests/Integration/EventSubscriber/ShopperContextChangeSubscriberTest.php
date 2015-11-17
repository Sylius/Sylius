<?php

namespace Sylius\Bundle\CoreBundle\Tests\Integration\EventSubscriber;

use Sylius\Bundle\CoreBundle\EventSubscriber\ShopperContextChangeSubscriber;
use Sylius\Bundle\CoreBundle\SyliusCoreEvents;
use Sylius\Bundle\CoreBundle\Tests\IntegrationTestCase;
use Symfony\Component\EventDispatcher\GenericEvent;

class ShopperContextChangeSubscriberTest extends IntegrationTestCase
{
    public function test_existence()
    {
        $this->assertInstanceOf(ShopperContextChangeSubscriber::class, $this->getService());
    }

    /**
     * @return ShopperContextChangeSubscriber
     */
    protected function getService()
    {
        return $this->container->get('sylius.shopper_context_change_subscriber');
    }

    public function test_it_calls_internal_cart_change_events()
    {
        $this->eventDispatcher->dispatch(
            SyliusCoreEvents::SHOPPER_CONTEXT_CHANGE
        );
    }

    public function test_it_calls_pre()
    {
        $this->eventDispatcher->dispatch(
            SyliusCoreEvents::PRE_CART_CHANGE
        );

        $this->eventDispatcher->dispatch(
            SyliusCoreEvents::CART_CHANGE
        );

        $this->eventDispatcher->dispatch(
            SyliusCoreEvents::POST_CART_CHANGE
        );
    }
}