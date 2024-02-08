<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

final class OrderItemUnitsTaxesApplicatorSpec extends ObjectBehavior
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

    function it_applies_taxes_on_units_based_on_item_total_and_rate(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        TaxRateResolverInterface $taxRateResolver,
        AdjustmentInterface $taxAdjustment1,
        AdjustmentInterface $taxAdjustment2,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        ProductVariantInterface $productVariant,
        TaxRateInterface $taxRate,
        ZoneInterface $zone,
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $orderItem->getVariant()->willReturn($productVariant);
        $taxRateResolver->resolve($productVariant, ['zone' => $zone])->willReturn($taxRate);

        $taxRate->getLabel()->willReturn('Simple tax (10%)');
        $taxRate->getCode()->willReturn('simple_tax');
        $taxRate->getName()->willReturn('Simple tax');
        $taxRate->getAmount()->willReturn(0.1);
        $taxRate->isIncludedInPrice()->willReturn(false);

        $orderItem->getUnits()->willReturn(new ArrayCollection([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $unit1->getTotal()->willReturn(1000);
        $calculator->calculate(1000, $taxRate)->willReturn(100);

        $unit2->getTotal()->willReturn(900);
        $calculator->calculate(900, $taxRate)->willReturn(90);

        $adjustmentsFactory
            ->createWithData(
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                100,
                false,
                [
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1,
                ],
            )
            ->willReturn($taxAdjustment1)
        ;
        $adjustmentsFactory
            ->createWithData(
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                90,
                false,
                [
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1,
                ],
            )
            ->willReturn($taxAdjustment2)
        ;

        $unit1->addAdjustment($taxAdjustment1)->shouldBeCalled();
        $unit2->addAdjustment($taxAdjustment2)->shouldBeCalled();

        $this->apply($order, $zone);
    }

    function it_does_nothing_if_order_item_has_no_units(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        TaxRateResolverInterface $taxRateResolver,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        TaxRateInterface $taxRate,
        ZoneInterface $zone,
    ): void {
        $orderItems = new ArrayCollection([$orderItem->getWrappedObject()]);
        $order->getItems()->willReturn($orderItems);

        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getUnits()->willReturn(new ArrayCollection());
        $taxRateResolver->resolve(Argument::cetera())->willReturn($taxRate);

        $calculator->calculate(Argument::cetera())->shouldNotBeCalled();
        $adjustmentsFactory->createWithData(Argument::cetera())->shouldNotBeCalled();

        $this->apply($order, $zone);
    }

    function it_does_nothing_if_tax_rate_cannot_be_resolved(
        CalculatorInterface $calculator,
        TaxRateResolverInterface $taxRateResolver,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ZoneInterface $zone,
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $orderItem->getQuantity()->willReturn(1);

        $orderItem->getVariant()->willReturn($productVariant);
        $taxRateResolver->resolve($productVariant, ['zone' => $zone])->willReturn(null);

        $orderItem->getUnits()->shouldNotBeCalled();
        $calculator->calculate(Argument::cetera())->shouldNotBeCalled();

        $this->apply($order, $zone);
    }

    function it_does_not_apply_taxes_with_amount_0(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        TaxRateResolverInterface $taxRateResolver,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        ProductVariantInterface $productVariant,
        TaxRateInterface $taxRate,
        ZoneInterface $zone,
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $orderItem->getQuantity()->willReturn(2);

        $orderItem->getVariant()->willReturn($productVariant);
        $taxRateResolver->resolve($productVariant, ['zone' => $zone])->willReturn($taxRate);

        $orderItem->getUnits()->willReturn(new ArrayCollection([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $unit1->getTotal()->willReturn(1000);
        $calculator->calculate(1000, $taxRate)->willReturn(0.00);

        $unit2->getTotal()->willReturn(900);
        $calculator->calculate(900, $taxRate)->willReturn(0.00);

        $adjustmentsFactory->createWithData(AdjustmentInterface::TAX_ADJUSTMENT, Argument::cetera())->shouldNotBeCalled();
        $unit1->addAdjustment(Argument::any())->shouldNotBeCalled();
        $unit2->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->apply($order, $zone);
    }

    function it_applies_taxes_on_units_based_on_item_total_and_rate_with_distribution_on_units(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        TaxRateResolverInterface $taxRateResolver,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        AdjustmentInterface $taxAdjustment1,
        AdjustmentInterface $taxAdjustment2,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        ProductVariantInterface $productVariant,
        TaxRateInterface $taxRate,
        ZoneInterface $zone,
    ): void {
        $this->beConstructedWith($calculator, $adjustmentsFactory, $taxRateResolver, $proportionalIntegerDistributor);

        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $orderItem->getVariant()->willReturn($productVariant);
        $taxRateResolver->resolve($productVariant, ['zone' => $zone])->willReturn($taxRate);

        $taxRate->getLabel()->willReturn('Simple tax (10%)');
        $taxRate->getCode()->willReturn('simple_tax');
        $taxRate->getName()->willReturn('Simple tax');
        $taxRate->getAmount()->willReturn(0.1004);
        $taxRate->isIncludedInPrice()->willReturn(false);

        $orderItem->getUnits()->willReturn(new ArrayCollection([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $unit1->getTotal()->willReturn(1000);
        $calculator->calculate(1000, $taxRate)->willReturn(100.40);

        $unit2->getTotal()->willReturn(900);
        $calculator->calculate(900, $taxRate)->willReturn(90.36);

        $proportionalIntegerDistributor->distribute([100, 90], 191)->willReturn([101, 90]);

        $adjustmentsFactory
            ->createWithData(
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                101,
                false,
                [
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1004,
                ],
            )
            ->willReturn($taxAdjustment1)
        ;
        $adjustmentsFactory
            ->createWithData(
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                90,
                false,
                [
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1004,
                ],
            )
            ->willReturn($taxAdjustment2)
        ;

        $unit1->addAdjustment($taxAdjustment1)->shouldBeCalled();
        $unit2->addAdjustment($taxAdjustment2)->shouldBeCalled();

        $this->apply($order, $zone);
    }
}
