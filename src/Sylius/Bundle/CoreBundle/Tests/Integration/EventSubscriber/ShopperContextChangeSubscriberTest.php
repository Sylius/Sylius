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
        $event = $this->prophet->prophesize(GenericEvent::class);

        $this->eventDispatcher->dispatch(
            SyliusCoreEvents::SHOPPER_CONTEXT_CHANGE ,
            $event->reveal()
        );
    }
}