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

use Sylius\Bundle\ApiBundle\Checker\AppliedCouponEligibilityCheckerInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class PromotionCouponEligibilityValidator extends ConstraintValidator
{
    public function __construct(
        private PromotionCouponRepositoryInterface $promotionCouponRepository,
        private OrderRepositoryInterface $orderRepository,
        private AppliedCouponEligibilityCheckerInterface $appliedCouponEligibilityChecker
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, UpdateCart::class);

        /** @var PromotionCouponEligibility $constraint */
        Assert::isInstanceOf($constraint, PromotionCouponEligibility::class);

        if ($value->couponCode === null) {
            return;
        }

        /** @var PromotionCouponInterface|null $promotionCoupon */
        $promotionCoupon = $this->promotionCouponRepository->findOneBy(['code' => $value->couponCode]);
        /** @var OrderInterface $cart */
        $cart = $this->orderRepository->findCartByTokenValue($value->getOrderTokenValue());

        $cart->setPromotionCoupon($promotionCoupon);

        if (!$this->appliedCouponEligibilityChecker->isEligible($promotionCoupon, $cart)) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('couponCode')
                ->addViolation()
            ;
        }
    }
}
