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
use Sylius\Component\Core\OrderProcessing\OrderRecalculator;
use Sylius\Component\Core\OrderProcessing\OrderRecalculatorInterface;
use Sylius\Component\Core\OrderProcessing\PricesRecalculatorInterface;
use Sylius\Component\Core\OrderProcessing\ShippingChargesProcessorInterface;
use Sylius\Component\Core\Taxation\OrderTaxesApplicatorInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessorInterface;

/**
 * @mixin OrderRecalculator
 *
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class OrderRecalculatorSpec extends ObjectBehavior
{
    function let(
        OrderTaxesApplicatorInterface $taxesApplicator,
        PricesRecalculatorInterface $pricesRecalculator,
        PromotionProcessorInterface $promotionProcessor,
        ShippingChargesProcessorInterface $shippingChargesProcessor
    ) {
        $this->beConstructedWith($taxesApplicator, $pricesRecalculator, $promotionProcessor, $shippingChargesProcessor);
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
        PricesRecalculatorInterface $pricesRecalculator,
        OrderTaxesApplicatorInterface $taxesApplicator,
        ShippingChargesProcessorInterface $shippingChargesProcessor,
        OrderInterface $order
    ) {
        $pricesRecalculator->recalculate($order);
        $promotionProcessor->process($order)->shouldBeCalled();
        $taxesApplicator->apply($order)->shouldBeCalled();
        $shippingChargesProcessor->applyShippingCharges($order)->shouldBeCalled();

        $this->recalculate($order);
    }
}
