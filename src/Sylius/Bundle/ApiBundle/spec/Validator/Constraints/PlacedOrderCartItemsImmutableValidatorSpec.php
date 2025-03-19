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

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Validator\Constraints\PlacedOrderCartItemsImmutable;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class PlacedOrderCartItemsImmutableValidatorSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($orderRepository);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_add_item_to_cart_command(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new \stdClass(), new PlacedOrderCartItemsImmutable()]);
    }

    function it_throws_an_exception_if_constraint_is_not_placed_order_cart_items_immutable(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [
                new AddItemToCart(orderTokenValue: 'orderTokenValue', productVariantCode: 'productVariantCode', quantity: 1),
                new NotNull(),
            ]);
    }

    function it_adds_violation_if_order_is_placed(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);

        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $orderRepository->findOneWithCompletedCheckout('orderTokenValue')->willReturn($order);

        $executionContext
            ->addViolation('sylius.order.cart_items_immutable')
            ->shouldBeCalled()
        ;

        $this->validate(
            new AddItemToCart(orderTokenValue: 'orderTokenValue', productVariantCode: 'productVariantCode', quantity: 1),
            new PlacedOrderCartItemsImmutable(),
        );
    }

    function it_does_nothing_if_checkout_is_not_completed(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);

        $orderRepository->findOneWithCompletedCheckout('orderTokenValue')->willReturn(null);

        $executionContext
            ->addViolation('sylius.order.cart_items_immutable')
            ->shouldNotBeCalled()
        ;

        $this->validate(
            new AddItemToCart(orderTokenValue: 'orderTokenValue', productVariantCode: 'productVariantCode', quantity: 1),
            new PlacedOrderCartItemsImmutable(),
        );
    }
}
