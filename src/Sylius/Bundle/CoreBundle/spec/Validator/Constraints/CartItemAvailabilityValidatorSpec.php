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
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemAvailability;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemAvailabilityValidator;
use Sylius\Bundle\InventoryBundle\Validator\Constraints\InStock;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CartItemAvailabilityValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext, AvailabilityCheckerInterface $availabilityChecker)
    {
        $this->beConstructedWith($availabilityChecker);

        $this->initialize($executionContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CartItemAvailabilityValidator::class);
    }

    function it_is_constraint_validator()
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_validates_only_add_cart_item_commands(OrderInterface $order)
    {
        $cartItemAvailabilityConstraint = new CartItemAvailability();

        $this->shouldThrow(\InvalidArgumentException::class)->during('validate', [$order, $cartItemAvailabilityConstraint]);
    }

    function it_is_cart_item_availability_validator(AddToCartCommandInterface $addCartItemCommand)
    {
        $inStockConstraint = new InStock();

        $this->shouldThrow(\InvalidArgumentException::class)->during('validate', [$addCartItemCommand, $inStockConstraint]);
    }

    function it_does_not_add_violation_if_requested_cart_item_is_available(
        ExecutionContextInterface $executionContext,
        AvailabilityCheckerInterface $availabilityChecker,
        AddToCartCommandInterface $addCartItemCommand,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant
    ) {
        $addCartItemCommand->getCart()->willReturn($order);
        $addCartItemCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getQuantity()->willReturn(10);
        $order->getItems()->willReturn([]);

        $availabilityChecker->isStockSufficient($productVariant, 10)->willReturn(true);

        $executionContext->addViolation(Argument::any(), Argument::any())->shouldNotBeCalled();

        $cartItemAvailabilityConstraint = new CartItemAvailability();

        $this->validate($addCartItemCommand, $cartItemAvailabilityConstraint);
    }

    function it_adds_violation_if_requested_cart_item_is_not_available(
        ExecutionContextInterface $executionContext,
        AvailabilityCheckerInterface $availabilityChecker,
        AddToCartCommandInterface $addCartItemCommand,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant
    ) {
        $addCartItemCommand->getCart()->willReturn($order);
        $addCartItemCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getQuantity()->willReturn(10);
        $order->getItems()->willReturn([]);
        $productVariant->getInventoryName()->willReturn('Mug');

        $availabilityChecker->isStockSufficient($productVariant, 10)->willReturn(false);

        $executionContext->addViolation('Insufficient stock', ['%itemName%' => 'Mug'])->shouldBeCalled();

        $cartItemAvailabilityConstraint = new CartItemAvailability();
        $cartItemAvailabilityConstraint->message = 'Insufficient stock';

        $this->validate($addCartItemCommand, $cartItemAvailabilityConstraint);
    }

    function it_adds_violation_if_total_quantity_of_cart_items_exceed_available_quantity(
        ExecutionContextInterface $executionContext,
        AvailabilityCheckerInterface $availabilityChecker,
        AddToCartCommandInterface $addCartItemCommand,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemInterface $existingOrderItem,
        ProductVariantInterface $productVariant
    ) {
        $addCartItemCommand->getCart()->willReturn($order);
        $addCartItemCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getQuantity()->willReturn(10);
        $productVariant->getInventoryName()->willReturn('Mug');

        $order->getItems()->willReturn([$existingOrderItem]);
        $existingOrderItem->getQuantity()->willReturn(10);
        $existingOrderItem->equals($orderItem)->willReturn(true);

        $availabilityChecker->isStockSufficient($productVariant, 20)->willReturn(false);

        $executionContext->addViolation('Insufficient stock', ['%itemName%' => 'Mug'])->shouldBeCalled();

        $cartItemAvailabilityConstraint = new CartItemAvailability();
        $cartItemAvailabilityConstraint->message = 'Insufficient stock';

        $this->validate($addCartItemCommand, $cartItemAvailabilityConstraint);
    }

    function it_does_not_add_violation_if_total_quantity_of_cart_items_do_not_exceed_available_quantity(
        ExecutionContextInterface $executionContext,
        AvailabilityCheckerInterface $availabilityChecker,
        AddToCartCommandInterface $addCartItemCommand,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemInterface $existingOrderItem,
        ProductVariantInterface $productVariant
    ) {
        $addCartItemCommand->getCart()->willReturn($order);
        $addCartItemCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getQuantity()->willReturn(10);
        $existingOrderItem->equals($orderItem)->willReturn(true);

        $order->getItems()->willReturn([$existingOrderItem]);
        $existingOrderItem->getQuantity()->willReturn(10);

        $availabilityChecker->isStockSufficient($productVariant, 20)->willReturn(true);

        $executionContext->addViolation(Argument::any(), Argument::any())->shouldNotBeCalled();

        $cartItemAvailabilityConstraint = new CartItemAvailability();
        $cartItemAvailabilityConstraint->message = 'Insufficient stock';

        $this->validate($addCartItemCommand, $cartItemAvailabilityConstraint);
    }
}
