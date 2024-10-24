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
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Checker\AppliedCouponEligibilityCheckerInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\Validator\Constraints\PromotionCouponEligibility;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class PromotionCouponEligibilityValidatorSpec extends ObjectBehavior
{
    function let(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderRepositoryInterface $orderRepository,
        AppliedCouponEligibilityCheckerInterface $appliedCouponEligibilityChecker,
    ): void {
        $this->beConstructedWith(
            $promotionCouponRepository,
            $orderRepository,
            $appliedCouponEligibilityChecker,
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
            ->during('validate', ['', new NotNull()])
        ;
    }

    function it_does_not_add_violation_if_coupon_code_was_not_provided(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderRepositoryInterface $orderRepository,
        AppliedCouponEligibilityCheckerInterface $appliedCouponEligibilityChecker,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);

        $value = new UpdateCart(orderTokenValue: 'token', couponCode: null);
        $constraint = new PromotionCouponEligibility();

        $promotionCouponRepository->findOneBy(Argument::any())->shouldNotBeCalled();
        $orderRepository->findCartByTokenValue(Argument::any())->shouldNotBeCalled();
        $appliedCouponEligibilityChecker->isEligible(Argument::cetera())->shouldNotBeCalled();
        $executionContext->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($value, $constraint);
    }

    function it_adds_violation_if_coupon_code_was_provided_but_it_does_not_exist(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderRepositoryInterface $orderRepository,
        AppliedCouponEligibilityCheckerInterface $appliedCouponEligibilityChecker,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $this->initialize($executionContext);

        $value = new UpdateCart(orderTokenValue: 'token', couponCode: 'couponCode');
        $constraint = new PromotionCouponEligibility();

        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn(null);
        $orderRepository->findCartByTokenValue('token')->shouldNotBeCalled();
        $appliedCouponEligibilityChecker->isEligible(Argument::cetera())->shouldNotBeCalled();

        $executionContext->buildViolation($constraint->invalid)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('couponCode')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setCode('COUPON_INVALID')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($value, $constraint);
    }

    function it_adds_violation_if_coupon_code_was_provided_but_it_is_expired(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderRepositoryInterface $orderRepository,
        AppliedCouponEligibilityCheckerInterface $appliedCouponEligibilityChecker,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        ExecutionContextInterface $executionContext,
        PromotionCouponInterface $promotionCoupon,
    ): void {
        $this->initialize($executionContext);

        $value = new UpdateCart(orderTokenValue: 'token', couponCode: 'couponCode');
        $constraint = new PromotionCouponEligibility();

        $promotionCoupon->getExpiresAt()->willReturn(new \DateTime('-1 day'));
        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn($promotionCoupon);
        $orderRepository->findCartByTokenValue('token')->shouldNotBeCalled();
        $appliedCouponEligibilityChecker->isEligible(Argument::cetera())->shouldNotBeCalled();

        $executionContext->buildViolation($constraint->expired)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('couponCode')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setCode('COUPON_EXPIRED')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($value, $constraint);
    }

    function it_does_not_add_violation_if_promotion_with_given_coupon_is_eligible(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderRepositoryInterface $orderRepository,
        AppliedCouponEligibilityCheckerInterface $appliedCouponEligibilityChecker,
        PromotionCouponInterface $promotionCoupon,
        OrderInterface $cart,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);
        $constraint = new PromotionCouponEligibility();

        $value = new UpdateCart(orderTokenValue: 'token', couponCode: 'couponCode');

        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn($promotionCoupon);
        $orderRepository->findCartByTokenValue('token')->willReturn($cart);

        $cart->setPromotionCoupon($promotionCoupon)->shouldBeCalled();

        $appliedCouponEligibilityChecker->isEligible($promotionCoupon, $cart)->willReturn(true);

        $executionContext->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($value, $constraint);
    }

    function it_adds_violation_if_promotion_with_given_coupon_is_not_eligible(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderRepositoryInterface $orderRepository,
        AppliedCouponEligibilityCheckerInterface $appliedCouponEligibilityChecker,
        PromotionCouponInterface $promotionCoupon,
        OrderInterface $cart,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $this->initialize($executionContext);

        $value = new UpdateCart(orderTokenValue: 'token', couponCode: 'couponCode');
        $constraint = new PromotionCouponEligibility();

        $promotionCoupon->getExpiresAt()->willReturn(null);
        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn($promotionCoupon);
        $orderRepository->findCartByTokenValue('token')->willReturn($cart);

        $cart->setPromotionCoupon($promotionCoupon)->shouldBeCalled();

        $appliedCouponEligibilityChecker->isEligible($promotionCoupon, $cart)->willReturn(false);

        $executionContext->buildViolation($constraint->ineligible)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('couponCode')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setCode('PROMOTION_INELIGIBLE')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($value, $constraint);
    }

    function it_throws_an_exception_if_cart_with_given_token_does_not_exist(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderRepositoryInterface $orderRepository,
        AppliedCouponEligibilityCheckerInterface $appliedCouponEligibilityChecker,
        PromotionCouponInterface $promotionCoupon,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);

        $value = new UpdateCart(orderTokenValue: 'token', couponCode: 'couponCode');
        $constraint = new PromotionCouponEligibility();

        $promotionCoupon->getExpiresAt()->willReturn(null);
        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn($promotionCoupon);
        $appliedCouponEligibilityChecker->isEligible(Argument::cetera())->shouldNotBeCalled();

        $orderRepository->findCartByTokenValue('token')->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$value, $constraint])
        ;
    }
}
