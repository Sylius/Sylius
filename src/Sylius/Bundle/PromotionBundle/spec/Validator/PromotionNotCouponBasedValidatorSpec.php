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

namespace spec\Sylius\Bundle\PromotionBundle\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionNotCouponBased;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class PromotionNotCouponBasedValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $context): void
    {
        $this->initialize($context);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_does_nothing_when_value_is_null(ExecutionContextInterface $context): void
    {
        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate(null, new PromotionNotCouponBased());
    }

    function it_throws_an_exception_when_constraint_is_not_promotion_not_coupon_based(
        ExecutionContextInterface $context,
        PromotionCouponInterface $coupon,
        Constraint $constraint,
    ): void {
        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [$coupon, $constraint])
        ;
    }

    function it_throws_an_exception_when_value_is_not_a_coupon(ExecutionContextInterface $context): void
    {
        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(UnexpectedValueException::class)
            ->during('validate', [new \stdClass(), new PromotionNotCouponBased()])
        ;
    }

    function it_does_nothing_when_coupon_has_no_promotion(
        ExecutionContextInterface $context,
        PromotionCouponInterface $coupon,
    ): void {
        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $coupon->getPromotion()->willReturn(null);

        $this->validate($coupon, new PromotionNotCouponBased());
    }

    function it_does_nothing_when_coupon_has_promotion_and_its_coupon_based(
        ExecutionContextInterface $context,
        PromotionCouponInterface $coupon,
        PromotionInterface $promotion,
    ): void {
        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $promotion->isCouponBased()->willReturn(true);
        $coupon->getPromotion()->willReturn($promotion);

        $this->validate($coupon, new PromotionNotCouponBased());
    }

    function it_adds_violation_when_coupon_has_promotion_but_its_not_coupon_based(
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $violationBuilder,
        PromotionCouponInterface $coupon,
        PromotionInterface $promotion,
    ): void {
        $constraint = new PromotionNotCouponBased();

        $context->buildViolation($constraint->message)->willReturn($violationBuilder);
        $violationBuilder->atPath('promotion')->willReturn($violationBuilder);
        $violationBuilder->addViolation()->shouldBeCalled();

        $promotion->isCouponBased()->willReturn(false);
        $coupon->getPromotion()->willReturn($promotion);

        $this->validate($coupon, $constraint);
    }
}
