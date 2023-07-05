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
use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
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

final class OrderItemsTaxesApplicatorSpec extends ObjectBehavior
{
    function let(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        IntegerDistributorInterface $distributor,
        TaxRateResolverInterface $taxRateResolver,
    ): void {
        $this->beConstructedWith($calculator, $adjustmentsFactory, $distributor, $taxRateResolver);
    }

    function it_implements_an_order_shipment_taxes_applicator_interface(): void
    {
        $this->shouldImplement(OrderTaxesApplicatorInterface::class);
    }

    function it_applies_taxes_on_units_based_on_item_total_and_rate_without_distribution_on_items(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        IntegerDistributorInterface $distributor,
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

        $orderItem->getQuantity()->willReturn(2);

        $orderItem->getVariant()->willReturn($productVariant);
        $taxRateResolver->resolve($productVariant, ['zone' => $zone])->willReturn($taxRate);

        $orderItem->getTotal()->willReturn(1000);
        $calculator->calculate(1000, $taxRate)->willReturn(100);

        $taxRate->getLabel()->willReturn('Simple tax (10%)');
        $taxRate->getCode()->willReturn('simple_tax');
        $taxRate->getName()->willReturn('Simple tax');
        $taxRate->getAmount()->willReturn(0.1);
        $taxRate->isIncludedInPrice()->willReturn(false);

        $orderItem->getUnits()->willReturn(new ArrayCollection([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $distributor->distribute(100, 2)->willReturn([50, 50]);

        $adjustmentsFactory
            ->createWithData(
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                50,
                false,
                [
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1,
                ],
            )
            ->willReturn($taxAdjustment1, $taxAdjustment2)
        ;

        $unit1->addAdjustment($taxAdjustment1)->shouldBeCalled();
        $unit2->addAdjustment($taxAdjustment2)->shouldBeCalled();

        $this->apply($order, $zone);
    }

    function it_throws_an_invalid_argument_exception_if_order_item_has_0_quantity(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ZoneInterface $zone,
    ): void {
        $items = new ArrayCollection([$orderItem->getWrappedObject()]);
        $order->getItems()->willReturn($items);

        $orderItem->getQuantity()->willReturn(0);

        $this->shouldThrow(\InvalidArgumentException::class)->during('apply', [$order, $zone]);
    }

    function it_does_nothing_if_tax_rate_cannot_be_resolved(
        TaxRateResolverInterface $taxRateResolver,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ZoneInterface $zone,
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $orderItem->getQuantity()->willReturn(5);
        $orderItem->getVariant()->willReturn($productVariant);
        $taxRateResolver->resolve($productVariant, ['zone' => $zone])->willReturn(null);

        $orderItem->getUnits()->shouldNotBeCalled();

        $this->apply($order, $zone);
    }

    function it_does_not_apply_taxes_with_amount_0(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        IntegerDistributorInterface $distributor,
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

        $orderItem->getTotal()->willReturn(1000);
        $calculator->calculate(1000, $taxRate)->willReturn(0);

        $taxRate->getLabel()->willReturn('Simple tax (0%)');
        $taxRate->isIncludedInPrice()->willReturn(false);

        $orderItem->getUnits()->willReturn(new ArrayCollection([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $distributor->distribute(0, 2)->willReturn([0, 0]);

        $adjustmentsFactory
            ->createWithData(AdjustmentInterface::TAX_ADJUSTMENT, 'Simple tax (0%)', 0, false)
            ->shouldNotBeCalled()
        ;

        $this->apply($order, $zone);
    }

    function it_applies_taxes_on_units_based_on_item_total_and_rate_with_distribution_on_items(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        IntegerDistributorInterface $distributor,
        TaxRateResolverInterface $taxRateResolver,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        AdjustmentInterface $taxAdjustment1,
        AdjustmentInterface $taxAdjustment2,
        AdjustmentInterface $taxAdjustment3,
        OrderInterface $order,
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        OrderItemUnitInterface $unit3,
        ProductVariantInterface $productVariant1,
        ProductVariantInterface $productVariant2,
        TaxRateInterface $taxRate,
        ZoneInterface $zone,
    ): void {
        $this->beConstructedWith($calculator, $adjustmentsFactory, $distributor, $taxRateResolver, $proportionalIntegerDistributor);

        $order->getItems()->willReturn(new ArrayCollection([
            $orderItem1->getWrappedObject(),
            $orderItem2->getWrappedObject(),
        ]));

        $orderItem1->getQuantity()->willReturn(2);
        $orderItem1->getTotal()->willReturn(1000);
        $orderItem1->getUnits()->willReturn(new ArrayCollection([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));
        $orderItem1->getVariant()->willReturn($productVariant1);
        $taxRateResolver->resolve($productVariant1, ['zone' => $zone])->willReturn($taxRate);

        $orderItem2->getQuantity()->willReturn(1);
        $orderItem2->getTotal()->willReturn(1000);
        $orderItem2->getUnits()->willReturn(new ArrayCollection([$unit3->getWrappedObject()]));
        $orderItem2->getVariant()->willReturn($productVariant2);
        $taxRateResolver->resolve($productVariant2, ['zone' => $zone])->willReturn($taxRate);

        $calculator->calculate(1000, $taxRate)->willReturn(100.40);

        $proportionalIntegerDistributor->distribute([100, 100], 201)->willReturn([101, 100]);

        $taxRate->getLabel()->willReturn('Simple tax (10%)');
        $taxRate->getCode()->willReturn('simple_tax');
        $taxRate->getName()->willReturn('Simple tax');
        $taxRate->getAmount()->willReturn(0.1);
        $taxRate->isIncludedInPrice()->willReturn(false);

        $distributor->distribute(101, 2)->willReturn([51, 50]);
        $distributor->distribute(100, 1)->willReturn([100]);

        $adjustmentsFactory
            ->createWithData(
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                51,
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
                50,
                false,
                [
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1,
                ],
            )
            ->willReturn($taxAdjustment2)
        ;
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
            ->willReturn($taxAdjustment3)
        ;

        $unit1->addAdjustment($taxAdjustment1)->shouldBeCalled();
        $unit2->addAdjustment($taxAdjustment2)->shouldBeCalled();
        $unit3->addAdjustment($taxAdjustment3)->shouldBeCalled();

        $this->apply($order, $zone);
    }

    function it_does_not_apply_taxes_with_distribution_on_items_if_the_given_item_has_no_tax_rate(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        IntegerDistributorInterface $distributor,
        TaxRateResolverInterface $taxRateResolver,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit,
        ProductVariantInterface $productVariant,
        ZoneInterface $zone,
    ): void {
        $this->beConstructedWith($calculator, $adjustmentsFactory, $distributor, $taxRateResolver, $proportionalIntegerDistributor);

        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $orderItem->getQuantity()->willReturn(1);
        $orderItem->getTotal()->willReturn(1000);
        $orderItem->getUnits()->willReturn(new ArrayCollection([$unit->getWrappedObject()]));
        $orderItem->getVariant()->willReturn($productVariant);
        $taxRateResolver->resolve($productVariant, ['zone' => $zone])->willReturn(null);

        $calculator->calculate(1000, Argument::any())->shouldNotBeCalled();

        $proportionalIntegerDistributor->distribute([0], 0)->willReturn([0, 0]);

        $distributor->distribute(Argument::any())->shouldNotBeCalled();
        $adjustmentsFactory->createWithData(Argument::any())->shouldNotBeCalled();

        $this->apply($order, $zone);
    }

    function it_throws_an_invalid_argument_exception_if_order_item_has_0_quantity_during_distribution_on_items(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        IntegerDistributorInterface $distributor,
        TaxRateResolverInterface $taxRateResolver,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ZoneInterface $zone,
    ): void {
        $this->beConstructedWith($calculator, $adjustmentsFactory, $distributor, $taxRateResolver, $proportionalIntegerDistributor);

        $items = new ArrayCollection([$orderItem->getWrappedObject()]);
        $order->getItems()->willReturn($items);

        $orderItem->getQuantity()->willReturn(0);

        $this->shouldThrow(\InvalidArgumentException::class)->during('apply', [$order, $zone]);
    }
}
