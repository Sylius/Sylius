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
use Prophecy\Argument;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderItemUnitsTaxesApplicator;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

/**
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
final class OrderItemUnitsTaxesApplicatorSpec extends ObjectBehavior
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
        $this->shouldHaveType(OrderItemUnitsTaxesApplicator::class);
    }

    function it_implements_an_order_shipment_taxes_applicator_interface()
    {
        $this->shouldImplement(OrderTaxesApplicatorInterface::class);
    }

    function it_applies_taxes_on_units_based_on_item_total_and_rate(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
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
    ) {
        $order->getItems()->willReturn($items);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn(new \ArrayIterator([$orderItem->getWrappedObject()]));

        $orderItem->getVariant()->willReturn($productVariant);
        $taxRateResolver->resolve($productVariant, ['zone' => $zone])->willReturn($taxRate);

        $taxRate->getLabel()->willReturn('Simple tax (10%)');
        $taxRate->isIncludedInPrice()->willReturn(false);

        $orderItem->getUnits()->willReturn($units);
        $units->getIterator()->willReturn(new \ArrayIterator([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $unit1->getTotal()->willReturn(1000);
        $calculator->calculate(1000, $taxRate)->willReturn(100);

        $unit2->getTotal()->willReturn(900);
        $calculator->calculate(900, $taxRate)->willReturn(90);

        $adjustmentsFactory
            ->createWithData(AdjustmentInterface::TAX_ADJUSTMENT, 'Simple tax (10%)', 100, false)
            ->willReturn($taxAdjustment1)
        ;
        $adjustmentsFactory
            ->createWithData(AdjustmentInterface::TAX_ADJUSTMENT, 'Simple tax (10%)', 90, false)
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
        ZoneInterface $zone
    ) {
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
        Collection $items,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ZoneInterface $zone
    ) {
        $order->getItems()->willReturn($items);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn(new \ArrayIterator([$orderItem->getWrappedObject()]));

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
        Collection $items,
        Collection $units,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        ProductVariantInterface $productVariant,
        TaxRateInterface $taxRate,
        ZoneInterface $zone
    ) {
        $order->getItems()->willReturn($items);

        $items->count()->willReturn(2);
        $items->getIterator()->willReturn(new \ArrayIterator([$orderItem->getWrappedObject()]));

        $orderItem->getQuantity()->willReturn(2);

        $orderItem->getVariant()->willReturn($productVariant);
        $taxRateResolver->resolve($productVariant, ['zone' => $zone])->willReturn($taxRate);

        $orderItem->getUnits()->willReturn($units);
        $units->getIterator()->willReturn(new \ArrayIterator([$unit1->getWrappedObject(), $unit2->getWrappedObject()]));

        $unit1->getTotal()->willReturn(1000);
        $calculator->calculate(1000, $taxRate)->willReturn(0);

        $unit2->getTotal()->willReturn(900);
        $calculator->calculate(900, $taxRate)->willReturn(0);

        $adjustmentsFactory->createWithData(AdjustmentInterface::TAX_ADJUSTMENT, Argument::cetera())->shouldNotBeCalled();
        $unit1->addAdjustment(Argument::any())->shouldNotBeCalled();
        $unit2->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->apply($order, $zone);
    }
}
