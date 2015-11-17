<?php

namespace Sylius\Bundle\CoreBundle\Tests\Integration\EventListener;

use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Tests\IntegrationTestCase;
use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Cart\SyliusCartEvents;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @group Integration
 *
 * @author  Piotr Walków <walkow.piotr@gmail.com>
 */
class CartInitializeEventTest extends IntegrationTestCase
{
    public function is_sets_channel_and_currency_on_order()
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
}
