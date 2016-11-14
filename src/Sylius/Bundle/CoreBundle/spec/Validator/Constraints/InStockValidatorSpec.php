<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Validator\Constraints\InStockValidator;
use Sylius\Bundle\InventoryBundle\Validator\Constraints\InStock;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class InStockValidatorSpec extends ObjectBehavior
{
    function let(
        AvailabilityCheckerInterface $availabilityChecker,
        CartContextInterface $cartContext,
        ExecutionContextInterface $executionContext
    ) {
        $this->beConstructedWith($availabilityChecker, $cartContext);
        $this->initialize($executionContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(InStockValidator::class);
    }

    function it_is_constraint_validator()
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_validates_inventory_stock_for_current_cart_item(
        AvailabilityCheckerInterface $availabilityChecker,
        CartContextInterface $cartContext,
        ExecutionContextInterface $executionContext,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemInterface $existingOrderItem,
        ProductVariantInterface $productVariant
    ) {
        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->getInventoryName()->willReturn('Mug');
        $inStockConstraint = new InStock();

        $cartContext->getCart()->willReturn($order);
        $order->getItems()->willReturn([$existingOrderItem]);
        $existingOrderItem->equals($orderItem)->willReturn(true);
        $existingOrderItem->getQuantity()->willReturn(10);
        $orderItem->getQuantity()->willReturn(15);

        $availabilityChecker->isStockSufficient($productVariant, 25)->willReturn(false);

        $executionContext->addViolation($inStockConstraint->message, ['%stockable%' => 'Mug'])->shouldBeCalled();

        $this->validate($orderItem, $inStockConstraint);
    }

    function it_does_not_add_violation_if_stock_is_sufficient(
        AvailabilityCheckerInterface $availabilityChecker,
        CartContextInterface $cartContext,
        ExecutionContextInterface $executionContext,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemInterface $existingOrderItem,
        ProductVariantInterface $productVariant
    ) {
        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->getInventoryName()->willReturn('Mug');
        $inStockConstraint = new InStock();

        $cartContext->getCart()->willReturn($order);
        $order->getItems()->willReturn([$existingOrderItem]);
        $existingOrderItem->equals($orderItem)->willReturn(true);
        $existingOrderItem->getQuantity()->willReturn(10);
        $orderItem->getQuantity()->willReturn(15);

        $availabilityChecker->isStockSufficient($productVariant, 25)->willReturn(true);

        $executionContext->addViolation($inStockConstraint->message, ['%stockable%' => 'Mug'])->shouldNotBeCalled();

        $this->validate($orderItem, $inStockConstraint);
    }

    function it_validates_inventory_stock_only_for_given_order_item(
        AvailabilityCheckerInterface $availabilityChecker,
        CartContextInterface $cartContext,
        ExecutionContextInterface $executionContext,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemInterface $existingOrderItem,
        ProductVariantInterface $productVariant
    ) {
        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->getInventoryName()->willReturn('Mug');
        $inStockConstraint = new InStock();

        $cartContext->getCart()->willReturn($order);
        $order->getItems()->willReturn([$existingOrderItem]);
        $existingOrderItem->equals($orderItem)->willReturn(false);
        $existingOrderItem->getQuantity()->willReturn(10);
        $orderItem->getQuantity()->willReturn(15);

        $availabilityChecker->isStockSufficient($productVariant, 15)->willReturn(false);

        $executionContext->addViolation($inStockConstraint->message, ['%stockable%' => 'Mug'])->shouldBeCalled();

        $this->validate($orderItem, $inStockConstraint);
    }

    function it_validates_inventory_stock_only_for_given_order_item_with_empty_cart(
        AvailabilityCheckerInterface $availabilityChecker,
        CartContextInterface $cartContext,
        ExecutionContextInterface $executionContext,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant
    ) {
        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->getInventoryName()->willReturn('Mug');
        $inStockConstraint = new InStock();

        $cartContext->getCart()->willReturn($order);
        $order->getItems()->willReturn([]);
        $orderItem->getQuantity()->willReturn(15);

        $availabilityChecker->isStockSufficient($productVariant, 15)->willReturn(false);

        $executionContext->addViolation($inStockConstraint->message, ['%stockable%' => 'Mug'])->shouldBeCalled();

        $this->validate($orderItem, $inStockConstraint);
    }
}
