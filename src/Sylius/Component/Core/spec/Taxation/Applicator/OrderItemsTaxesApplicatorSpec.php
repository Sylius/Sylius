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
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
final class OrderItemsTaxesApplicatorSpec extends ObjectBehavior
{
    function let(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        IntegerDistributorInterface $distributor,
        TaxRateResolverInterface $taxRateResolver
    ): void {
        $this->beConstructedWith($calculator, $adjustmentsFactory, $distributor, $taxRateResolver);
    }

    function it_implements_an_order_shipment_taxes_applicator_interface(): void
    {
        $this->shouldImplement(OrderTaxesApplicatorInterface::class);
    }

    function it_applies_taxes_on_units_based_on_item_total_and_rate(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        IntegerDistributorInterface $distributor,
        TaxRateResolverInterface $taxRateResolver,
        AdjustmentInterface $taxAdjustment1,
        AdjustmentInterface $taxAdjustment2,
        Collection $items,
        Collection $units,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        ProductVariantInterface $productVariant,
        TaxRateInterface $taxRate,
        ZoneInterface $zone
    ): void {
        $order->getItems()->willReturn($items);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn(new \ArrayIterator([$orderItem->getWrappedObject()]));

        $orderItem->getQuantity()->willReturn(2);

        $orderItem->getVariant()->willReturn($productVariant);
        $taxRateResolver->resolve($productVariant, ['zone' => $zone])->willReturn($taxRate);

        $orderItem->getTotal()->willReturn(1000);
        $calculator->calculate(1000, $taxRate)->willReturn(100);

        $taxRate->getLabel()->willReturn('Simple tax (10%)');
        $taxRate->isIncludedInPrice()->willReturn(false);

        $orderItem->getUnits()->willReturn($units);
        $units->getIterator()->willReturn(new \ArrayIterator([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $distributor->distribute(100, 2)->willReturn([50, 50]);

        $adjustmentsFactory
            ->createWithData(AdjustmentInterface::TAX_ADJUSTMENT, 'Simple tax (10%)', 50, false)
            ->willReturn($taxAdjustment1, $taxAdjustment2)
        ;

        $unit1->addAdjustment($taxAdjustment1)->shouldBeCalled();
        $unit2->addAdjustment($taxAdjustment2)->shouldBeCalled();

        $this->apply($order, $zone);
    }

    function it_throws_an_invalid_argument_exception_if_order_item_has_0_quantity(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ZoneInterface $zone
    ): void {
        $items = new ArrayCollection([$orderItem->getWrappedObject()]);
        $order->getItems()->willReturn($items);

        $orderItem->getQuantity()->willReturn(0);

        $this->shouldThrow(\InvalidArgumentException::class)->during('apply', [$order, $zone]);
    }

    function it_does_nothing_if_tax_rate_cannot_be_resolved(
        TaxRateResolverInterface $taxRateResolver,
        Collection $items,
        \Iterator $iterator,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ZoneInterface $zone
    ): void {
        $order->getItems()->willReturn($items);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false)->shouldBeCalled();
        $iterator->current()->willReturn($orderItem);
        $iterator->next()->shouldBeCalled();

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
        Collection $items,
        Collection $units,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        ProductVariantInterface $productVariant,
        TaxRateInterface $taxRate,
        ZoneInterface $zone
    ): void {
        $order->getItems()->willReturn($items);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn(new \ArrayIterator([$orderItem->getWrappedObject()]));

        $orderItem->getQuantity()->willReturn(2);
        $orderItem->getVariant()->willReturn($productVariant);

        $taxRateResolver->resolve($productVariant, ['zone' => $zone])->willReturn($taxRate);

        $orderItem->getTotal()->willReturn(1000);
        $calculator->calculate(1000, $taxRate)->willReturn(0);

        $taxRate->getLabel()->willReturn('Simple tax (0%)');
        $taxRate->isIncludedInPrice()->willReturn(false);

        $orderItem->getUnits()->willReturn($units);
        $units->getIterator()->willReturn(new \ArrayIterator([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $distributor->distribute(0, 2)->willReturn([0, 0]);

        $adjustmentsFactory
            ->createWithData(AdjustmentInterface::TAX_ADJUSTMENT, 'Simple tax (0%)', 0, false)
            ->shouldNotBeCalled()
        ;

        $this->apply($order, $zone);
    }
}
