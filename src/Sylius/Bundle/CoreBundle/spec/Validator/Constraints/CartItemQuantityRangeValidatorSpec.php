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
use Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemQuantityRange;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class CartItemQuantityRangeValidatorSpec extends ObjectBehavior
{
    const MESSAGE = 'sylius.cart_item.quantity.not_in_range';

    function let(PropertyAccessorInterface $propertyAccessor, ExecutionContextInterface $context): void
    {
        $this->beConstructedWith($propertyAccessor, 17);

        $this->initialize($context);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_cart_item_quantity_range(
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [9, $constraint])
        ;
    }

    function it_does_nothing_if_value_is_empty(
        ExecutionContextInterface $context,
    ): void {
        $this->validate(null, new CartItemQuantityRange(min: 1));

        $context->buildViolation(self::MESSAGE)->shouldNotHaveBeenCalled();
    }

    function it_does_nothing_if_value_is_in_range(
        RepositoryInterface $countryRepository,
        ExecutionContextInterface $context,
        CountryInterface $country,
    ): void {
        $this->validate(5, new CartItemQuantityRange(min: 1));

        $context->buildViolation(self::MESSAGE)->shouldNotHaveBeenCalled();
    }

    function it_adds_a_violation_if_value_is_not_in_range(
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $constraintViolationBuilder->addViolation()->shouldBeCalled();
        $constraintViolationBuilder->setCode(CartItemQuantityRange::NOT_IN_RANGE_ERROR)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath(Argument::any())->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setParameter(Argument::cetera())->willReturn($constraintViolationBuilder);

        $context->buildViolation(self::MESSAGE)->shouldBeCalled()->willReturn($constraintViolationBuilder);

        $this->validate(18, new CartItemQuantityRange(notInRangeMessage: self::MESSAGE, min: 1));
    }
}
