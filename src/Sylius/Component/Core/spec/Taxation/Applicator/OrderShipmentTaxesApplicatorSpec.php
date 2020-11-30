<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Taxation\Applicator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

final class OrderShipmentTaxesApplicatorSpec extends ObjectBehavior
{
    function let(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        TaxRateResolverInterface $taxRateResolver
    ): void {
        $this->beConstructedWith($calculator, $adjustmentsFactory, $taxRateResolver);
    }

    function it_implements_an_order_shipment_taxes_applicator_interface(): void
    {
        $this->shouldImplement(OrderTaxesApplicatorInterface::class);
    }

    function it_applies_shipment_taxes_on_order_based_on_shipment_adjustments_promotions_and_rate(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        TaxRateResolverInterface $taxRateResolver,
        AdjustmentInterface $shippingTaxAdjustment,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        TaxRateInterface $taxRate,
        ZoneInterface $zone
    ): void {
        $order->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));
        $shipment->getMethod()->willReturn($shippingMethod);
        $taxRateResolver->resolve($shippingMethod, ['zone' => $zone])->willReturn($taxRate);

        $shippingMethod->getCode()->willReturn('fedex');
        $shippingMethod->getName()->willReturn('FedEx');

        $taxRate->getLabel()->willReturn('Simple tax (10%)');
        $taxRate->getCode()->willReturn('simple_tax');
        $taxRate->getName()->willReturn('Simple tax');
        $taxRate->getAmount()->willReturn(0.1);
        $taxRate->isIncludedInPrice()->willReturn(false);

        $order->getShippingTotal()->willReturn(1000);

        $calculator->calculate(1000, $taxRate)->willReturn(100);

        $adjustmentsFactory
            ->createWithData(
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                100,
                false,
                [
                    'shippingMethodCode' => 'fedex',
                    'shippingMethodName' => 'FedEx',
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1,
                ])
            ->willReturn($shippingTaxAdjustment)
        ;
        $order->addAdjustment($shippingTaxAdjustment)->shouldBeCalled();

        $this->apply($order, $zone);
    }

    function it_does_nothing_if_the_tax_amount_is_0(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        TaxRateResolverInterface $taxRateResolver,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        TaxRateInterface $taxRate,
        ZoneInterface $zone
    ): void {
        $order->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));
        $shipment->getMethod()->willReturn($shippingMethod);
        $taxRateResolver->resolve($shippingMethod, ['zone' => $zone])->willReturn($taxRate);

        $order->getShippingTotal()->willReturn(1000);

        $calculator->calculate(1000, $taxRate)->willReturn(0.00);

        $adjustmentsFactory->createWithData(Argument::cetera())->shouldNotBeCalled();
        $order->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->apply($order, $zone);
    }

    function it_throws_exception_if_order_has_no_shipment_but_shipment_total_is_greater_than_0(
        OrderInterface $order,
        ZoneInterface $zone
    ): void {
        $order->getShippingTotal()->willReturn(10);
        $order->getShipments()->willReturn(new ArrayCollection([]));

        $this->shouldThrow(\LogicException::class)->during('apply', [$order, $zone]);
    }

    function it_does_nothing_if_tax_rate_cannot_be_resolved(
        CalculatorInterface $calculator,
        TaxRateResolverInterface $taxRateResolver,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        ZoneInterface $zone
    ): void {
        $order->getShippingTotal()->willReturn(100);
        $order->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));
        $shipment->getMethod()->willReturn($shippingMethod);

        $taxRateResolver->resolve($shippingMethod, ['zone' => $zone])->willReturn(null);

        $calculator->calculate(Argument::any())->shouldNotBeCalled();
        $order->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->apply($order, $zone);
    }

    function it_does_nothing_if_order_has_0_shipping_total(
        TaxRateResolverInterface $taxRateResolver,
        OrderInterface $order,
        ZoneInterface $zone
    ): void {
        $order->getShippingTotal()->willReturn(0);

        $taxRateResolver->resolve(Argument::any())->shouldNotBeCalled();
        $order->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->apply($order, $zone);
    }
}
