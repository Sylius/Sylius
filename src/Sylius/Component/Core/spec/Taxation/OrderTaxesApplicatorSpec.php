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
use Sylius\Bundle\CoreBundle\OrderProcessing\OrderTaxesApplicatorInterface;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Core\Provider\DefaultTaxZoneProviderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderTaxesApplicatorSpec extends ObjectBehavior
{
    function let(
        CalculatorInterface $calculator,
        DefaultTaxZoneProviderInterface $defaultTaxZoneProvider,
        FactoryInterface $adjustmentFactory,
        IntegerDistributorInterface $integerDistributor,
        TaxRateResolverInterface $taxRateResolver,
        ZoneMatcherInterface $zoneMatcher
    ) {
        $this->beConstructedWith(
            $calculator,
            $defaultTaxZoneProvider,
            $adjustmentFactory,
            $integerDistributor,
            $taxRateResolver,
            $zoneMatcher
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Taxation\OrderTaxesApplicator');
    }

    function it_implements_Sylius_taxation_processor_interface()
    {
        $this->shouldImplement(OrderTaxesApplicatorInterface::class);
    }

    function it_distributes_calculated_taxes_for_items_units(
        $adjustmentFactory,
        $calculator,
        $integerDistributor,
        $taxRateResolver,
        $zoneMatcher,
        AddressInterface $address,
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2,
        \Iterator $iterator,
        Collection $items,
        Collection $units,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        ProductInterface $product,
        TaxRateInterface $taxRate,
        ZoneInterface $zone
    ) {
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $order->getItems()->willReturn($items);
        $order->isEmpty()->willReturn(false);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($iterator, $iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false, true, false)->shouldBeCalled();
        $iterator->current()->willReturn($orderItem, $orderItem);
        $iterator->next()->shouldBeCalled();

        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $order->getShippingAddress()->willReturn($address);
        $zoneMatcher->match($address)->willReturn($zone);

        $orderItem->getProduct()->willReturn($product);
        $taxRateResolver->resolve($product, array('zone' => $zone))->willReturn($taxRate);

        $orderItem->getTotal()->willReturn(1000);
        $taxRate->getAmountAsPercentage()->willReturn(20);
        $taxRate->getName()->willReturn('tax');
        $taxRate->isIncludedInPrice()->willReturn(false);

        $calculator->calculate(1000, $taxRate)->willReturn(200);

        $orderItem->getUnits()->willReturn($units);
        $orderItem->getQuantity()->willReturn(2);

        $integerDistributor->distribute(2, 200)->willReturn(array(100, 100));

        $units->get(0)->willReturn($unit1);
        $units->get(1)->willReturn($unit2);

        $adjustmentFactory->createNew()->willReturn($adjustment1, $adjustment2);

        $adjustment1->setType(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $adjustment1->setAmount(100)->shouldBeCalled();
        $adjustment1->setDescription('tax (20%)')->shouldBeCalled();
        $adjustment1->setNeutral(false)->shouldBeCalled();

        $adjustment2->setType(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $adjustment2->setAmount(100)->shouldBeCalled();
        $adjustment2->setDescription('tax (20%)')->shouldBeCalled();
        $adjustment2->setNeutral(false)->shouldBeCalled();

        $unit1->addAdjustment($adjustment1)->shouldBeCalled();
        $unit2->addAdjustment($adjustment2)->shouldBeCalled();

        $this->apply($order);
    }

    function it_does_not_apply_taxes_if_there_is_no_order_item(Collection $items, OrderInterface $order)
    {
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $order->getItems()->willReturn(array());
        $order->isEmpty()->willReturn(true);

        $order->getShippingAddress()->shouldNotBeCalled();

        $this->apply($order);
    }

    function it_does_not_apply_taxes_if_there_is_no_tax_zone(
        $defaultTaxZoneProvider,
        $taxRateResolver,
        $zoneMatcher,
        AddressInterface $address,
        \Iterator $iterator,
        Collection $items,
        OrderInterface $order,
        OrderItemInterface $orderItem
    ) {
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $order->getItems()->willReturn($items);
        $order->isEmpty()->willReturn(false);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false)->shouldBeCalled();
        $iterator->current()->willReturn($orderItem);
        $iterator->next()->shouldBeCalled();

        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $order->getShippingAddress()->willReturn($address);
        $zoneMatcher->match($address)->willReturn(null);
        $defaultTaxZoneProvider->getZone()->willReturn(null);

        $taxRateResolver->resolve(Argument::any())->shouldNotBeCalled();

        $this->apply($order);
    }

    function it_does_not_apply_taxes_to_item_units_if_tax_rate_cannot_be_resolved(
        $integerDistributor,
        $taxRateResolver,
        $zoneMatcher,
        AddressInterface $address,
        \Iterator $iterator,
        Collection $items,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductInterface $product,
        ZoneInterface $zone
    ) {
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $order->getItems()->willReturn($items);
        $order->isEmpty()->willReturn(false);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false)->shouldBeCalled();
        $iterator->current()->willReturn($orderItem);
        $iterator->next()->shouldBeCalled();

        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $order->getShippingAddress()->willReturn($address);
        $zoneMatcher->match($address)->willReturn($zone);
        $orderItem->getProduct()->willReturn($product);

        $taxRateResolver->resolve($product, array('zone' => $zone))->willReturn(null);

        $integerDistributor->distribute(Argument::any())->shouldNotBeCalled();

        $this->apply($order);
    }
}
