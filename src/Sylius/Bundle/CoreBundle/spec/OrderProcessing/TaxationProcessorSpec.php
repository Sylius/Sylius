<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\OrderProcessing;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Event\AdjustmentEvent;
use Sylius\Bundle\SettingsBundle\Model\Settings;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Taxation\Model\TaxRate;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TaxationProcessorSpec extends ObjectBehavior
{
    function let(
        CalculatorInterface $calculator,
        TaxRateResolverInterface $taxRateResolver,
        ZoneMatcherInterface $zoneMatcher,
        Settings $taxationSettings,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith(
            $calculator,
            $taxRateResolver,
            $zoneMatcher,
            $taxationSettings,
            $eventDispatcher
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\OrderProcessing\TaxationProcessor');
    }

    function it_implements_Sylius_taxation_processor_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\OrderProcessing\TaxationProcessorInterface');
    }

    function it_does_not_apply_any_taxes_if_zone_is_missing(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        InventoryUnitInterface $inventoryUnit,
        $taxationSettings
    ) {
        $orderItems = new ArrayCollection();
        $orderItems->add($orderItem->getWrappedObject());

        $inventoryUnits = new ArrayCollection();
        $inventoryUnits->add($inventoryUnit->getWrappedObject());

        $order->getInventoryUnits()->willReturn($inventoryUnits);

        $order->getItems()->willReturn($orderItems);
        $orderItem->getInventoryUnits()->willReturn($inventoryUnits);

        $inventoryUnit->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $order->getShippingAddress()->willReturn(null);

        $taxationSettings->has('default_tax_zone')->willReturn(false);

        $this->applyTaxes($order);
        $inventoryUnit->addAdjustment(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_adds_tax_to_inventory_unit_within_order_item(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        InventoryUnitInterface $inventoryUnit,
        $taxationSettings,
        TaxRateResolverInterface $taxRateResolver,
        ZoneInterface $zone,
        ProductInterface $product,
        TaxRate $taxRate,
        CalculatorInterface $calculator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $orderItems = new ArrayCollection();
        $orderItems->add($orderItem->getWrappedObject());

        $inventoryUnits = new ArrayCollection();
        $inventoryUnits->add($inventoryUnit->getWrappedObject());

        $order->getInventoryUnits()->willReturn($inventoryUnits);
        $order->getItems()->willReturn($orderItems);
        $orderItem->getInventoryUnits()->willReturn($inventoryUnits);

        $inventoryUnit->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled(2);

        $order->getShippingAddress()->willReturn(null);

        $taxationSettings->has('default_tax_zone')->willReturn(true);
        $taxationSettings->get('default_tax_zone')->willReturn($zone);

        $orderItem->getProduct()->willReturn($product);

        $taxRateResolver->resolve($product, array('zone' => $zone))->willReturn($taxRate);
        $orderItem->getUnitPrice()->willReturn(100);

        $calculator->calculate(100, $taxRate)->willReturn(23);
        $taxRate->getId()->willReturn(666);
        $taxRate->getAmount()->willReturn(23);
        $taxRate->getAmountAsPercentage()->willReturn(46);
        $taxRate->getName()->willReturn('WAR TAX');
        $taxRate->isIncludedInPrice()->willReturn(false);

        $eventDispatcher->dispatch(
            AdjustmentEvent::ADJUSTMENT_ADDING_INVENTORY_UNIT,
            Argument::type(AdjustmentEvent::class)
        )->shouldBeCalled();

        $this->applyTaxes($order);
    }

    function it_adds_tax_to_inventory_units_within_multiple_order_items(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemInterface $orderItem2,
        InventoryUnitInterface $inventoryUnit,
        InventoryUnitInterface $inventoryUnit2,
        $taxationSettings,
        TaxRateResolverInterface $taxRateResolver,
        ZoneInterface $zone,
        ProductInterface $product,
        TaxRate $taxRate,
        CalculatorInterface $calculator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $orderItems = new ArrayCollection();
        $orderItems->add($orderItem->getWrappedObject());
        $orderItems->add($orderItem2->getWrappedObject());

        $inventoryUnits = new ArrayCollection();
        $inventoryUnits2 = new ArrayCollection();

        $allInventoryUnits = new ArrayCollection();
        $allInventoryUnits->add($inventoryUnit->getWrappedObject());
        $allInventoryUnits->add($inventoryUnit2->getWrappedObject());

        $order->getInventoryUnits()->willReturn($allInventoryUnits);

        $inventoryUnits->add($inventoryUnit->getWrappedObject());
        $inventoryUnits2->add($inventoryUnit2->getWrappedObject());

        $order->getItems()->willReturn($orderItems);

        $orderItem->getInventoryUnits()->willReturn($inventoryUnits);
        $orderItem2->getInventoryUnits()->willReturn($inventoryUnits2);

        $inventoryUnit->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $inventoryUnit2->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $order->getShippingAddress()->willReturn(null);

        $taxationSettings->has('default_tax_zone')->willReturn(true);
        $taxationSettings->get('default_tax_zone')->willReturn($zone);

        $orderItem->getProduct()->willReturn($product);
        $orderItem2->getProduct()->willReturn($product);

        $taxRateResolver->resolve($product, array('zone' => $zone))->willReturn($taxRate);
        $orderItem->getUnitPrice()->willReturn(100);
        $orderItem2->getUnitPrice()->willReturn(300);

        $calculator->calculate(100, $taxRate)->willReturn(23);
        $calculator->calculate(300, $taxRate)->willReturn(29);
        $taxRate->getId()->willReturn(666);
        $taxRate->getAmount()->willReturn(23);
        $taxRate->getAmountAsPercentage()->willReturn(46);
        $taxRate->getName()->willReturn('WAR TAX');
        $taxRate->isIncludedInPrice()->willReturn(false);

        $eventDispatcher->dispatch(
            AdjustmentEvent::ADJUSTMENT_ADDING_INVENTORY_UNIT,
            Argument::type(AdjustmentEvent::class)
        )->shouldBeCalled(2);

        $this->applyTaxes($order);
    }

}
