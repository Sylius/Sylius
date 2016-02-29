<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\OrderProcessing;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\OrderRecalculatorInterface;
use Sylius\Component\Core\OrderProcessing\ShippingChargesProcessorInterface;
use Sylius\Component\Core\Taxation\OrderTaxesApplicatorInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessorInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class OrderRecalculatorSpec extends ObjectBehavior
{
    function let(
        PromotionProcessorInterface $promotionProcessor,
        OrderTaxesApplicatorInterface $taxesApplicator,
        ShippingChargesProcessorInterface $shippingChargesProcessor
    ) {
        $this->beConstructedWith($promotionProcessor, $taxesApplicator, $shippingChargesProcessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\OrderProcessing\OrderRecalculator');
    }

    function it_implements_order_recalculator_interface()
    {
        $this->shouldImplement(OrderRecalculatorInterface::class);
    }

    function it_recalculates_order_promotions_taxes_and_shipping_charges(
        PromotionProcessorInterface $promotionProcessor,
        OrderTaxesApplicatorInterface $taxesApplicator,
        ShippingChargesProcessorInterface $shippingChargesProcessor,
        OrderInterface $order,
        PromotionInterface $firstPromotion,
        PromotionInterface $secondPromotion
    ) {
        $order->getPromotions()->willReturn([$firstPromotion, $secondPromotion]);

        $promotionProcessor->process($order)->shouldBeCalled();
        $taxesApplicator->apply($order)->shouldBeCalled();
        $shippingChargesProcessor->applyShippingCharges($order)->shouldBeCalled();

        $this->recalculate($order);
    }

    function it_recalculates_order_taxes_and_shipping_charges(
        OrderTaxesApplicatorInterface $taxesApplicator,
        ShippingChargesProcessorInterface $shippingChargesProcessor,
        OrderInterface $order
    ) {
        $order->getPromotions()->willReturn([]);

        $taxesApplicator->apply($order)->shouldBeCalled();
        $shippingChargesProcessor->applyShippingCharges($order)->shouldBeCalled();

        $this->recalculate($order);
    }
}
