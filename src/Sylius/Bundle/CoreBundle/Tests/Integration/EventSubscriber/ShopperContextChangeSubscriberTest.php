<?php

namespace Sylius\Bundle\CoreBundle\Tests\Integration\EventSubscriber;

use Sylius\Bundle\CoreBundle\EventSubscriber\ShopperContextChangeSubscriber;
use Sylius\Bundle\CoreBundle\SyliusCoreEvents;
use Sylius\Bundle\CoreBundle\Tests\IntegrationTestCase;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
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

    /**
     * @test
     */
    public function something()
    {
        $serviceId = 'sylius.cart_provider';

        $mockedCartProvider = new MockedCartProvider();

        $this->container->set($serviceId, $mockedCartProvider);
    }
}

class MockedCartProvider implements CartProviderInterface
{
    /**
     * @inheritDoc
     */
    public function hasCart()
    {
        // TODO: Implement hasCart() method.
    }

    /**
     * @inheritDoc
     */
    public function getCart()
    {
        // TODO: Implement getCart() method.
    }

    /**
     * @inheritDoc
     */
    public function setCart(CartInterface $cart)
    {
        // TODO: Implement setCart() method.
    }

    /**
     * @inheritDoc
     */
    public function abandonCart()
    {
        // TODO: Implement abandonCart() method.
    }

}