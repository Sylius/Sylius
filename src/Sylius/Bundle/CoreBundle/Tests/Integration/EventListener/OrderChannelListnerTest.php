<?php

namespace Sylius\Bundle\CoreBundle\Tests\Integration\EventListener;

use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Tests\IntegrationTestCase;
use Sylius\Bundle\CoreBundle\EventListener\OrderChannelListener;
use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Cart\SyliusCartEvents;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @group Integration
 *
 * @author  Piotr Walków <walkow.piotr@gmail.com>
 */
class OrderChannelListenerTest extends IntegrationTestCase
{
    public function test_existence()
    {
        $this->assertInstanceOf(
            OrderChannelListener::class,
            $this->getService()
        );
    }

    /**
     * Checks also if the currency has been set
     *
     * @TODO extract to SyliusCartEventIntegration tests
     */
    public function test_it_sets_channel_on_order()
    {
        /** @var OrderInterface $order */
        $order = $this->prophet->prophesize(OrderInterface::class);

        $event = new CartEvent($order->reveal());

        // OrderChannelListener
        $order->setChannel(Argument::any())->shouldBeCalled();
        // OrderCurrencyListener
        $order->setCurrency(Argument::any())->shouldBeCalled();

        $this->eventDispatcher->dispatch(SyliusCartEvents::CART_INITIALIZE, $event);
    }

    /**
     * @return OrderChannelListener
     */
    private function getService()
    {
        return $this->container->get('sylius.listener.order_channel');
    }
}
