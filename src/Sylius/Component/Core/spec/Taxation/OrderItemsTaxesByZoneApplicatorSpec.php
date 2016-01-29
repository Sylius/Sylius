<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Taxation;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Distributor\IntegerDistributorInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Taxation\OrderItemsTaxesByZoneApplicatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderItemsTaxesByZoneApplicatorSpec extends ObjectBehavior
{
    function let(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        IntegerDistributorInterface $distributor,
        TaxRateResolverInterface $taxRateResolver
    ) {
        $this->beConstructedWith($calculator, $adjustmentsFactory, $distributor, $taxRateResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Taxation\OrderItemsTaxesByZoneApplicator');
    }

    function it_implements_order_shipment_taxes_applicator_interface()
    {
        $this->shouldImplement(OrderItemsTaxesByZoneApplicatorInterface::class);
    }

    function it_applies_taxes_on_units_based_on_item_total_and_rate(
        $adjustmentsFactory,
        $calculator,
        $distributor,
        $taxRateResolver,
        AdjustmentInterface $taxAdjustment1,
        AdjustmentInterface $taxAdjustment2,
        Collection $items,
        Collection $units,
        \Iterator $iterator,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        ProductInterface $product,
        TaxRateInterface $taxRate,
        ZoneInterface $zone
    ) {
        $order->getItems()->willReturn($items);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false)->shouldBeCalled();
        $iterator->current()->willReturn($orderItem);
        $iterator->next()->shouldBeCalled();

        $orderItem->getQuantity()->willReturn(2);

        $orderItem->getProduct()->willReturn($product);
        $taxRateResolver->resolve($product, ['zone' => $zone])->willReturn($taxRate);

        $orderItem->getTotal()->willReturn(1000);
        $calculator->calculate(1000, $taxRate)->willReturn(100);

        $taxRate->getLabel()->willReturn('Simple tax (10%)');
        $taxRate->isIncludedInPrice()->willReturn(false);

        $orderItem->getUnits()->willReturn($units);

        $units->current()->willReturn($unit1, $unit2);
        $units->next()->shouldBeCalledTimes(2);

        $distributor->distribute(100, 2)->willReturn([50, 50]);

        $adjustmentsFactory->createWithData(AdjustmentInterface::TAX_ADJUSTMENT, 'Simple tax (10%)', 50, false)->willReturn($taxAdjustment1, $taxAdjustment2);

        $unit1->addAdjustment($taxAdjustment1)->shouldBeCalled();
        $unit2->addAdjustment($taxAdjustment2)->shouldBeCalled();

        $this->apply($order, $zone);
    }

    function it_does_nothing_if_order_item_has_no_units(
        $taxRateResolver,
        Collection $items,
        \Iterator $iterator,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ZoneInterface $zone
    ) {
        $order->getItems()->willReturn($items);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false)->shouldBeCalled();
        $iterator->current()->willReturn($orderItem);
        $iterator->next()->shouldBeCalled();

        $orderItem->getQuantity()->willReturn(0);
        $taxRateResolver->resolve(Argument::any())->shouldNotBeCalled();

        $this->apply($order, $zone);
    }

    function it_does_nothing_if_tax_rate_cannot_be_resolved(
        $taxRateResolver,
        Collection $items,
        \Iterator $iterator,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductInterface $product,
        ZoneInterface $zone
    ) {
        $order->getItems()->willReturn($items);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false)->shouldBeCalled();
        $iterator->current()->willReturn($orderItem);
        $iterator->next()->shouldBeCalled();

        $orderItem->getQuantity()->willReturn(5);

        $orderItem->getProduct()->willReturn($product);
        $taxRateResolver->resolve($product, ['zone' => $zone])->willReturn(null);

        $orderItem->getUnits()->shouldNotBeCalled();

        $this->apply($order, $zone);
    }

    function it_does_not_apply_taxes_with_amount_0(
        $adjustmentsFactory,
        $calculator,
        $distributor,
        $taxRateResolver,
        AdjustmentInterface $taxAdjustment1,
        AdjustmentInterface $taxAdjustment2,
        Collection $items,
        Collection $units,
        \Iterator $iterator,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductInterface $product,
        TaxRateInterface $taxRate,
        ZoneInterface $zone
    ) {
        $order->getItems()->willReturn($items);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false)->shouldBeCalled();
        $iterator->current()->willReturn($orderItem);
        $iterator->next()->shouldBeCalled();

        $orderItem->getQuantity()->willReturn(2);

        $orderItem->getProduct()->willReturn($product);
        $taxRateResolver->resolve($product, ['zone' => $zone])->willReturn($taxRate);

        $orderItem->getTotal()->willReturn(1000);
        $calculator->calculate(1000, $taxRate)->willReturn(0);

        $taxRate->getLabel()->willReturn('Simple tax (0%)');
        $taxRate->isIncludedInPrice()->willReturn(false);

        $orderItem->getUnits()->willReturn($units);
        $units->current()->shouldNotBeCalled();
        $units->next()->shouldNotBeCalled();

        $distributor->distribute(0, 2)->willReturn([0, 0]);

        $adjustmentsFactory->createWithData(AdjustmentInterface::TAX_ADJUSTMENT, 'Simple tax (0%)', 0, false)->willReturn($taxAdjustment1, $taxAdjustment2);

        $this->apply($order, $zone);
    }
}
