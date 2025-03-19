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

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemVariantEnabled;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class CartItemVariantEnabledValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext): void
    {
        $this->initialize($executionContext);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_cart_item_variant_enabled(
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [[], $constraint])
        ;
    }

    function it_throws_an_exception_if_value_is_not_an_add_to_cart_command(): void
    {
        $this
            ->shouldThrow(UnexpectedValueException::class)
            ->during('validate', ['', new CartItemVariantEnabled()])
        ;
    }

    function it_does_nothing_if_variant_is_null(
        ExecutionContextInterface $executionContext,
        AddToCartCommandInterface $addCartItemCommand,
        OrderItemInterface $orderItem,
    ): void {
        $addCartItemCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getVariant()->willReturn(null);

        $executionContext->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($addCartItemCommand, new CartItemVariantEnabled());
    }

    function it_adds_violation_if_variant_is_not_enabled(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        AddToCartCommandInterface $addCartItemCommand,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
    ): void {
        $addCartItemCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getVariant()->willReturn(null);
        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->getInventoryName()->willReturn('Mug');
        $productVariant->isEnabled()->willReturn(false);

        $executionContext->buildViolation('sylius.cart_item.variant.not_available')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setParameter('%variantName%', 'Mug')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('cartItem.variant')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($addCartItemCommand, new CartItemVariantEnabled());
    }
}
