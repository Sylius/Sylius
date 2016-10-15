<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Taxation\Applicator;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderShipmentTaxesApplicator;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

/**
 * @mixin OrderShipmentTaxesApplicator
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
final class OrderShipmentTaxesApplicatorSpec extends ObjectBehavior
{
    function let(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        TaxRateResolverInterface $taxRateResolver
    ) {
        $this->beConstructedWith($calculator, $adjustmentsFactory, $taxRateResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderShipmentTaxesApplicator::class);
    }

    function it_implements_an_order_shipment_taxes_applicator_interface()
    {
        $this->shouldImplement(OrderTaxesApplicatorInterface::class);
    }

    function it_applies_shipment_taxes_on_order_based_on_shipment_adjustments_promotions_and_rate(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        TaxRateResolverInterface $taxRateResolver,
        AdjustmentInterface $shippingTaxAdjustment,
        Collection $shippingAdjustments,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        TaxRateInterface $taxRate,
        ZoneInterface $zone
    ) {
        $order->getLastShipment()->willReturn($shipment);
        $shipment->getMethod()->willReturn($shippingMethod);
        $taxRateResolver->resolve($shippingMethod, ['zone' => $zone])->willReturn($taxRate);

        $taxRate->getLabel()->willReturn('Simple tax (10%)');
        $taxRate->isIncludedInPrice()->willReturn(false);

        $order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->willReturn($shippingAdjustments);
        $shippingAdjustments->isEmpty()->willReturn(false);

        $order->getShippingTotal()->willReturn(1000);

        $calculator->calculate(1000, $taxRate)->willReturn(100);

        $adjustmentsFactory
            ->createWithData(AdjustmentInterface::TAX_ADJUSTMENT, 'Simple tax (10%)', 100, false)
            ->willReturn($shippingTaxAdjustment)
        ;
        $order->addAdjustment($shippingTaxAdjustment)->shouldBeCalled();

        $this->apply($order, $zone);
    }

    function it_does_nothing_if_there_are_no_shipment_taxes_on_order(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        TaxRateResolverInterface $taxRateResolver,
        Collection $shippingAdjustments,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        TaxRateInterface $taxRate,
        ZoneInterface $zone
    ) {
        $order->getLastShipment()->willReturn($shipment);
        $shipment->getMethod()->willReturn($shippingMethod);
        $taxRateResolver->resolve($shippingMethod, ['zone' => $zone])->willReturn($taxRate);

        $order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->willReturn($shippingAdjustments);
        $shippingAdjustments->isEmpty()->willReturn(false);

        $order->getShippingTotal()->willReturn(1000);

        $calculator->calculate(1000, $taxRate)->willReturn(0);

        $adjustmentsFactory->createWithData(Argument::cetera())->shouldNotBeCalled();
        $order->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->apply($order, $zone);
    }

    function it_does_nothing_if_order_has_no_shipment(
        TaxRateResolverInterface $taxRateResolver,
        OrderInterface $order,
        ZoneInterface $zone
    ) {
        $order->getLastShipment()->willReturn(null);
        $taxRateResolver->resolve(Argument::any())->shouldNotBeCalled();

        $this->apply($order, $zone);
    }

    function it_does_nothing_if_tax_rate_cannot_be_resolved(
        TaxRateResolverInterface $taxRateResolver,
        Collection $shippingAdjustments,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        ZoneInterface $zone
    ) {
        $order->getLastShipment()->willReturn($shipment);
        $shipment->getMethod()->willReturn($shippingMethod);

        $order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->willReturn($shippingAdjustments);
        $shippingAdjustments->isEmpty()->willReturn(false);

        $taxRateResolver->resolve($shippingMethod, ['zone' => $zone])->willReturn(null);

        $this->apply($order, $zone);
    }

    function it_does_nothing_if_order_has_no_shipping_adjustments(
        TaxRateResolverInterface $taxRateResolver,
        Collection $shippingAdjustments,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ZoneInterface $zone
    ) {
        $order->getLastShipment()->willReturn($shipment);

        $order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->willReturn($shippingAdjustments);
        $shippingAdjustments->isEmpty()->willReturn(true);

        $taxRateResolver->resolve(Argument::any())->shouldNotBeCalled();

        $this->apply($order, $zone);
    }
}
