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
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, UpdateCart::class);

        /** @var PromotionCouponEligibility $constraint */
        Assert::isInstanceOf($constraint, PromotionCouponEligibility::class);

        if ($value->couponCode === null) {
            return;
        }

        /** @var PromotionCouponInterface|null $promotionCoupon */
        $promotionCoupon = $this->promotionCouponRepository->findOneBy(['code' => $value->couponCode]);

        if ($promotionCoupon === null) {
            $this->addViolation($constraint->invalid, 'COUPON_INVALID');

            return;
        }

        $expirationDate = $promotionCoupon->getExpiresAt();

        if ($expirationDate !== null && $expirationDate < new \DateTime()) {
            $this->addViolation($constraint->expired, 'COUPON_EXPIRED');

            return;
        }

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValue($value->orderTokenValue);

        if ($cart === null) {
            throw new \InvalidArgumentException('Cart with given token supposed to exist at this point.');
        }

        $cart->setPromotionCoupon($promotionCoupon);

        if (!$this->appliedCouponEligibilityChecker->isEligible($promotionCoupon, $cart)) {
            $this->addViolation($constraint->ineligible, 'PROMOTION_INELIGIBLE');
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
