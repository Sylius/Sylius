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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Bundle\ApiBundle\Command\Cart\ApplyCouponToCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class PromotionCouponEligibilityValidator extends ConstraintValidator
{
    private PromotionCouponRepositoryInterface $promotionCouponRepository;

    private OrderRepositoryInterface $orderRepository;

    private PromotionEligibilityCheckerInterface $promotionChecker;

    private PromotionCouponEligibilityCheckerInterface $promotionCouponChecker;

    public function __construct(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderRepositoryInterface $orderRepository,
        PromotionEligibilityCheckerInterface $promotionChecker,
        PromotionCouponEligibilityCheckerInterface $promotionCouponChecker
    ) {
        $this->promotionCouponRepository = $promotionCouponRepository;
        $this->orderRepository = $orderRepository;
        $this->promotionChecker = $promotionChecker;
        $this->promotionCouponChecker = $promotionCouponChecker;
    }

    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, ApplyCouponToCart::class);

        if ($value->couponCode === null) {
            return;
        }

        /** @var PromotionCouponInterface $promotionCoupon */
        $promotionCoupon = $this->promotionCouponRepository->findOneBy(['code' => $value->couponCode]);

        if (null === $promotionCoupon) {
            $this->context->buildViolation('sylius.promotion_coupon.is_invalid')
                ->atPath('couponCode')
                ->addViolation()
            ;

            return;
        }

        /** @var OrderInterface $cart */
        $cart = $this->orderRepository->findCartByTokenValue($value->getOrderTokenValue());

        $cart->setPromotionCoupon($promotionCoupon);

        if (!$this->promotionCouponChecker->isEligible($cart, $promotionCoupon)) {
            $this->context->buildViolation('sylius.promotion_coupon.is_invalid')
                ->atPath('couponCode')
                ->addViolation()
            ;

            return;
        }

        if (!$this->promotionChecker->isEligible($cart, $promotionCoupon->getPromotion())) {
            $this->context->buildViolation('sylius.promotion.is_invalid')
                ->atPath('couponCode')
                ->addViolation()
            ;
        }
    }
}
