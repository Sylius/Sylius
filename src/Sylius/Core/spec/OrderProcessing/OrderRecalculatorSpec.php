<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Core\OrderProcessing;

use PhpSpec\ObjectBehavior;
use Sylius\Core\Model\OrderInterface;
use Sylius\Core\OrderProcessing\OrderRecalculator;
use Sylius\Core\OrderProcessing\OrderRecalculatorInterface;
use Sylius\Core\OrderProcessing\PricesRecalculatorInterface;
use Sylius\Core\OrderProcessing\ShippingChargesProcessorInterface;
use Sylius\Core\Remover\AdjustmentsRemoverInterface;
use Sylius\Core\Taxation\Processor\OrderTaxesProcessorInterface;
use Sylius\Promotion\Processor\PromotionProcessorInterface;

/**
 * @mixin OrderRecalculator
 *
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class OrderRecalculatorSpec extends ObjectBehavior
{
    function let(
        AdjustmentsRemoverInterface $adjustmentsRemover,
        OrderTaxesProcessorInterface $taxesProcessor,
        PricesRecalculatorInterface $pricesRecalculator,
        PromotionProcessorInterface $promotionProcessor,
        ShippingChargesProcessorInterface $shippingChargesProcessor
    ) {
        $this->beConstructedWith(
            $adjustmentsRemover,
            $taxesProcessor,
            $pricesRecalculator,
            $promotionProcessor,
            $shippingChargesProcessor
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Core\OrderProcessing\OrderRecalculator');
    }

    function it_implements_order_recalculator_interface()
    {
        $this->shouldImplement(OrderRecalculatorInterface::class);
    }

    function it_recalculates_order_promotions_taxes_and_shipping_charges(
        AdjustmentsRemoverInterface $adjustmentsRemover,
        PromotionProcessorInterface $promotionProcessor,
        PricesRecalculatorInterface $pricesRecalculator,
        OrderTaxesProcessorInterface $taxesProcessor,
        ShippingChargesProcessorInterface $shippingChargesProcessor,
        OrderInterface $order
    ) {
        $adjustmentsRemover->removeFrom($order)->shouldBeCalled();
        $pricesRecalculator->recalculate($order)->shouldBeCalled();
        $promotionProcessor->process($order)->shouldBeCalled();
        $taxesProcessor->process($order)->shouldBeCalled();
        $shippingChargesProcessor->applyShippingCharges($order)->shouldBeCalled();

        $this->recalculate($order);
    }
}
