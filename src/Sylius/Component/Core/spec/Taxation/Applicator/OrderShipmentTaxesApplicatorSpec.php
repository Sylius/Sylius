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
        TaxRateResolverInterface $taxRateResolver,
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
        ZoneInterface $zone,
    ): void {
        $order->getShippingTotal()->willReturn(1000);
        $order->hasShipments()->willReturn(true);
        $order->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));
        $shipment->getAdjustmentsTotal()->willReturn(1000);
        $shipment->getMethod()->willReturn($shippingMethod);

        $shippingMethod->getCode()->willReturn('fedex');
        $shippingMethod->getName()->willReturn('FedEx');

        $taxRateResolver->resolve($shippingMethod, ['zone' => $zone])->willReturn($taxRate);
        $taxRate->getLabel()->willReturn('Simple tax (10%)');
        $taxRate->getCode()->willReturn('simple_tax');
        $taxRate->getName()->willReturn('Simple tax');
        $taxRate->getAmount()->willReturn(0.1);
        $taxRate->isIncludedInPrice()->willReturn(false);

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
                ],
            )
            ->willReturn($shippingTaxAdjustment)
        ;
        $shipment->addAdjustment($shippingTaxAdjustment)->shouldBeCalled();

        $this->apply($order, $zone);
    }

    function it_applies_taxes_on_multiple_shipments_based_on_shipment_adjustments_promotions_and_rate(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        TaxRateResolverInterface $taxRateResolver,
        AdjustmentInterface $firstShippingTaxAdjustment,
        AdjustmentInterface $secondShippingTaxAdjustment,
        OrderInterface $order,
        ShipmentInterface $firstShipment,
        ShipmentInterface $secondShipment,
        ShippingMethodInterface $shippingMethod,
        TaxRateInterface $taxRate,
        ZoneInterface $zone,
    ): void {
        $order->getShippingTotal()->willReturn(1000);
        $order->hasShipments()->willReturn(true);
        $order->getShipments()->willReturn(new ArrayCollection([
            $firstShipment->getWrappedObject(),
            $secondShipment->getWrappedObject(),
        ]));
        $firstShipment->getAdjustmentsTotal()->willReturn(600);
        $firstShipment->getMethod()->willReturn($shippingMethod);
        $secondShipment->getAdjustmentsTotal()->willReturn(400);
        $secondShipment->getMethod()->willReturn($shippingMethod);

        $shippingMethod->getCode()->willReturn('fedex');
        $shippingMethod->getName()->willReturn('FedEx');

        $taxRateResolver->resolve($shippingMethod, ['zone' => $zone])->willReturn($taxRate);
        $taxRate->getLabel()->willReturn('Simple tax (10%)');
        $taxRate->getCode()->willReturn('simple_tax');
        $taxRate->getName()->willReturn('Simple tax');
        $taxRate->getAmount()->willReturn(0.1);
        $taxRate->isIncludedInPrice()->willReturn(false);

        $calculator->calculate(600, $taxRate)->willReturn(60);
        $adjustmentsFactory
            ->createWithData(
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                60,
                false,
                [
                    'shippingMethodCode' => 'fedex',
                    'shippingMethodName' => 'FedEx',
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1,
                ],
            )
            ->willReturn($firstShippingTaxAdjustment)
        ;
        $firstShipment->addAdjustment($firstShippingTaxAdjustment)->shouldBeCalled();

        $calculator->calculate(400, $taxRate)->willReturn(40);
        $adjustmentsFactory
            ->createWithData(
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                40,
                false,
                [
                    'shippingMethodCode' => 'fedex',
                    'shippingMethodName' => 'FedEx',
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1,
                ],
            )
            ->willReturn($secondShippingTaxAdjustment)
        ;
        $secondShipment->addAdjustment($secondShippingTaxAdjustment)->shouldBeCalled();

        $this->apply($order, $zone);
    }

    function it_applies_taxes_on_multiple_shipments_when_there_is_no_tax_rate_for_one_of_them(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        TaxRateResolverInterface $taxRateResolver,
        AdjustmentInterface $shippingTaxAdjustment,
        OrderInterface $order,
        ShipmentInterface $firstShipment,
        ShipmentInterface $secondShipment,
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        TaxRateInterface $taxRate,
        ZoneInterface $zone,
    ): void {
        $order->getShippingTotal()->willReturn(1000);
        $order->hasShipments()->willReturn(true);
        $order->getShipments()->willReturn(new ArrayCollection([
            $firstShipment->getWrappedObject(),
            $secondShipment->getWrappedObject(),
        ]));
        $firstShipment->getAdjustmentsTotal()->willReturn(600);
        $firstShipment->getMethod()->willReturn($firstShippingMethod);
        $secondShipment->getAdjustmentsTotal()->willReturn(400);
        $secondShipment->getMethod()->willReturn($secondShippingMethod);

        $firstShippingMethod->getCode()->willReturn('dhl');
        $firstShippingMethod->getName()->willReturn('DHL');

        $secondShippingMethod->getCode()->willReturn('fedex');
        $secondShippingMethod->getName()->willReturn('FedEx');

        $taxRateResolver->resolve($firstShippingMethod, ['zone' => $zone])->willReturn(null);

        $taxRateResolver->resolve($secondShippingMethod, ['zone' => $zone])->willReturn($taxRate);
        $taxRate->getLabel()->willReturn('Simple tax (10%)');
        $taxRate->getCode()->willReturn('simple_tax');
        $taxRate->getName()->willReturn('Simple tax');
        $taxRate->getAmount()->willReturn(0.1);
        $taxRate->isIncludedInPrice()->willReturn(false);

        $firstShipment->addAdjustment(Argument::any())->shouldNotBeCalled();

        $calculator->calculate(400, $taxRate)->willReturn(40);
        $adjustmentsFactory
            ->createWithData(
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                40,
                false,
                [
                    'shippingMethodCode' => 'fedex',
                    'shippingMethodName' => 'FedEx',
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1,
                ],
            )
            ->willReturn($shippingTaxAdjustment)
        ;
        $secondShipment->addAdjustment($shippingTaxAdjustment)->shouldBeCalled();

        $this->apply($order, $zone);
    }

    function it_applies_taxes_on_multiple_shipments_when_one_of_them_has_0_tax_amount(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        TaxRateResolverInterface $taxRateResolver,
        AdjustmentInterface $shippingTaxAdjustment,
        OrderInterface $order,
        ShipmentInterface $firstShipment,
        ShipmentInterface $secondShipment,
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        TaxRateInterface $taxRate,
        ZoneInterface $zone,
    ): void {
        $order->getShippingTotal()->willReturn(1000);
        $order->hasShipments()->willReturn(true);
        $order->getShipments()->willReturn(new ArrayCollection([
            $firstShipment->getWrappedObject(),
            $secondShipment->getWrappedObject(),
        ]));
        $firstShipment->getAdjustmentsTotal()->willReturn(600);
        $firstShipment->getMethod()->willReturn($firstShippingMethod);
        $secondShipment->getAdjustmentsTotal()->willReturn(400);
        $secondShipment->getMethod()->willReturn($secondShippingMethod);

        $firstShippingMethod->getCode()->willReturn('dhl');
        $firstShippingMethod->getName()->willReturn('DHL');

        $secondShippingMethod->getCode()->willReturn('fedex');
        $secondShippingMethod->getName()->willReturn('FedEx');

        $taxRateResolver->resolve($firstShippingMethod, ['zone' => $zone])->willReturn($taxRate);
        $taxRateResolver->resolve($secondShippingMethod, ['zone' => $zone])->willReturn($taxRate);
        $taxRate->getLabel()->willReturn('Simple tax (10%)');
        $taxRate->getCode()->willReturn('simple_tax');
        $taxRate->getName()->willReturn('Simple tax');
        $taxRate->getAmount()->willReturn(0.1);
        $taxRate->isIncludedInPrice()->willReturn(false);

        $firstShipment->addAdjustment(Argument::any())->shouldNotBeCalled();

        $calculator->calculate(600, $taxRate)->willReturn(0);
        $adjustmentsFactory
            ->createWithData(
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                0,
                false,
                [
                    'shippingMethodCode' => 'dhl',
                    'shippingMethodName' => 'DHL',
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1,
                ],
            )
            ->shouldNotBeCalled()
        ;

        $calculator->calculate(400, $taxRate)->willReturn(40);
        $adjustmentsFactory
            ->createWithData(
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                40,
                false,
                [
                    'shippingMethodCode' => 'fedex',
                    'shippingMethodName' => 'FedEx',
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1,
                ],
            )
            ->willReturn($shippingTaxAdjustment)
        ;
        $secondShipment->addAdjustment($shippingTaxAdjustment)->shouldBeCalled();

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
        ZoneInterface $zone,
    ): void {
        $order->getShippingTotal()->willReturn(1000);
        $order->hasShipments()->willReturn(true);
        $order->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));
        $shipment->getMethod()->willReturn($shippingMethod);
        $shipment->getAdjustmentsTotal()->willReturn(1000);

        $taxRateResolver->resolve($shippingMethod, ['zone' => $zone])->willReturn($taxRate);

        $calculator->calculate(1000, $taxRate)->willReturn(0.00);

        $adjustmentsFactory->createWithData(Argument::cetera())->shouldNotBeCalled();
        $order->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->apply($order, $zone);
    }

    function it_throws_an_exception_if_order_has_no_shipment_but_shipment_total_is_greater_than_0(
        OrderInterface $order,
        ZoneInterface $zone,
    ): void {
        $order->getShippingTotal()->willReturn(10);
        $order->hasShipments()->willReturn(false);

        $this->shouldThrow(\LogicException::class)->during('apply', [$order, $zone]);
    }

    function it_does_nothing_if_tax_rate_cannot_be_resolved(
        CalculatorInterface $calculator,
        TaxRateResolverInterface $taxRateResolver,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        ZoneInterface $zone,
    ): void {
        $order->getShippingTotal()->willReturn(100);
        $order->hasShipments()->willReturn(true);
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
        ZoneInterface $zone,
    ): void {
        $order->getShippingTotal()->willReturn(0);

        $taxRateResolver->resolve(Argument::any())->shouldNotBeCalled();
        $order->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->apply($order, $zone);
    }
}
