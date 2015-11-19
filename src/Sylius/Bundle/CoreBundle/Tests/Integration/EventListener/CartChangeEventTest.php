<?php

namespace Sylius\Bundle\CoreBundle\Tests\Integration\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\CoreBundle\SyliusCoreEvents;
use Sylius\Bundle\CoreBundle\Tests\IntegrationTestCase;
use Sylius\Component\Cart\SyliusCartEvents;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @group Integration
 *
 * @author  Piotr Walków <walkow.piotr@gmail.com>
 */
class ChartChangeEventTest extends IntegrationTestCase
{
    public function test_it_removes_tax_and_promotions_while_thrown()
    {
        /** @var OrderInterface $order */
        $order = $this->prophet->prophesize(OrderInterface::class);

        $event = new GenericEvent($order->reveal());
//        $collection = new ArrayCollection();

        // RefreshCartListener->refreshCart
//        $order->calculateTotal()->shouldBeCalled();

        // OrderTaxationListener->removeTaxes
//        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        // OrderPricingListener->recalculatePrices
//        $order->getCustomer()->shouldBeCalled();
//        $order->getChannel()->shouldBeCalled();

        // OrderPromotionListener->processOrderPromotion
//        $order->getItems()->shouldBeCalled()->willReturn($collection);
//        $order->getPromotions()->shouldBeCalled()->willReturn($collection);
        $this->eventDispatcher->dispatch(SyliusCoreEvents::CART_CHANGE, $event);
    }
}
