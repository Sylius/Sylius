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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Bundle\ApiBundle\Checker\AppliedCouponEligibilityCheckerInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class PromotionCouponEligibilityValidator extends ConstraintValidator
{
    /**
     * @param PromotionCouponRepositoryInterface<PromotionCouponInterface> $promotionCouponRepository
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     */
    public function __construct(
        private readonly PromotionCouponRepositoryInterface $promotionCouponRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly AppliedCouponEligibilityCheckerInterface $appliedCouponEligibilityChecker,
        private readonly ?PromotionCouponEligibilityCheckerInterface $durationEligibilityChecker = null,
    ) {
        if (null === $this->durationEligibilityChecker) {
            trigger_deprecation(
                'sylius/api-bundle',
                '2.3',
                'Not passing a "%s" as the 4th argument of "%s" is deprecated and will be required in Sylius 3.0.',
                PromotionCouponEligibilityCheckerInterface::class,
                self::class,
            );
        }
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, UpdateCart::class);

        /** @var PromotionCouponEligibility $constraint */
        Assert::isInstanceOf($constraint, PromotionCouponEligibility::class);

        if ($value->couponCode === null) {
            return;
        }

        $promotionCoupon = $this->promotionCouponRepository->findOneBy(['code' => $value->couponCode]);

        if (!$promotionCoupon instanceof PromotionCouponInterface) {
            $this->addViolation($constraint->invalidMessage, PromotionCouponEligibility::PROMOTION_COUPON_INVALID_ERROR);

            return;
        }

        $cart = $this->orderRepository->findCartByTokenValue($value->orderTokenValue);
        Assert::isInstanceOf($cart, OrderInterface::class, sprintf('Cart with token value "%s" does not exist.', $value->orderTokenValue));

        $cart->setPromotionCoupon($promotionCoupon);

        if (null !== $this->durationEligibilityChecker && !$this->durationEligibilityChecker->isEligible($cart, $promotionCoupon)) {
            $this->addViolation($constraint->expiredMessage, PromotionCouponEligibility::PROMOTION_COUPON_EXPIRED_ERROR);

            return;
        }

        if (!$this->appliedCouponEligibilityChecker->isEligible($promotionCoupon, $cart)) {
            $this->addViolation($constraint->ineligibleMessage, PromotionCouponEligibility::PROMOTION_COUPON_INELIGIBLE_ERROR);
        }
    }

    private function addViolation(string $message, string $code): void
    {
        $this->context
            ->buildViolation($message)
            ->atPath('couponCode')
            ->setCode($code)
            ->addViolation()
        ;
    }
}
