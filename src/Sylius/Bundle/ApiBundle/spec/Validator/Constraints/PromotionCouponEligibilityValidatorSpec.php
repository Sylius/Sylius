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

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Checker\AppliedCouponEligibilityCheckerInterface;
use Sylius\Bundle\ApiBundle\Command\Cart\ApplyCouponToCart;
use Sylius\Bundle\ApiBundle\Validator\Constraints\PromotionCouponEligibility;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class PromotionCouponEligibilityValidatorSpec extends ObjectBehavior
{
    function let(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderRepositoryInterface $orderRepository,
        AppliedCouponEligibilityCheckerInterface $appliedCouponEligibilityChecker
    ): void {
        $this->beConstructedWith(
            $promotionCouponRepository,
            $orderRepository,
            $appliedCouponEligibilityChecker
        );
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_constraint_is_not_of_expected_type(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', new class() extends Constraint {}])
        ;
    }

    function it_does_not_add_violation_if_promotion_coupon_is_eligible(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        AppliedCouponEligibilityCheckerInterface $appliedCouponEligibilityChecker,
        PromotionCouponInterface $promotionCoupon,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $cart,
        ExecutionContextInterface $executionContext
    ): void {
        $this->initialize($executionContext);
        $constraint = new PromotionCouponEligibility();

        $value = new ApplyCouponToCart('couponCode');
        $value->setOrderTokenValue('token');

        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn($promotionCoupon);
        $orderRepository->findCartByTokenValue('token')->willReturn($cart);

        $cart->setPromotionCoupon($promotionCoupon)->shouldBeCalled();

        $appliedCouponEligibilityChecker->isEligible($promotionCoupon, $cart)->willReturn(true);

        $executionContext->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($value, $constraint);
    }

    function it_adds_violation_if_promotion_coupon_is_not_eligible(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        AppliedCouponEligibilityCheckerInterface $appliedCouponEligibilityChecker,
        PromotionCouponInterface $promotionCoupon,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $cart,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder
    ): void {
        $this->initialize($executionContext);
        $constraint = new PromotionCouponEligibility();
        $constraint->message = 'message';

        $value = new ApplyCouponToCart('couponCode');
        $value->setOrderTokenValue('token');

        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn($promotionCoupon);
        $orderRepository->findCartByTokenValue('token')->willReturn($cart);

        $cart->setPromotionCoupon($promotionCoupon)->shouldBeCalled();

        $appliedCouponEligibilityChecker->isEligible($promotionCoupon, $cart)->willReturn(false);

        $executionContext->buildViolation($constraint->message)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('couponCode')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($value, $constraint);
    }
}
