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

use DateTime;
use Sylius\Bundle\ApiBundle\Command\Cart\ApplyCouponToCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class PromotionCouponEligibilityValidator extends ConstraintValidator
{
    /** @var PromotionCouponRepositoryInterface */
    private $promotionCouponRepository;

    public function __construct(PromotionCouponRepositoryInterface $promotionCouponRepository)
    {
        $this->promotionCouponRepository = $promotionCouponRepository;
    }


    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, ApplyCouponToCart::class);

        /** @var PromotionCouponInterface $promotionCoupon */
        $promotionCoupon = $this->promotionCouponRepository->findOneBy(['code' => $value->couponCode]);

        $couponUsageLimit = $promotionCoupon->getUsageLimit();

        if (null !== $couponUsageLimit || $couponUsageLimit === 0) {
            $this->context->buildViolation('sylius.promotion_coupon.usage_limit.expired')
                ->atPath('couponCode')
                ->addViolation()
            ;
        }

        $couponExpireDate = $promotionCoupon->getExpiresAt();

        if (null !== $couponExpireDate && $couponExpireDate < new DateTime()) {
            $this->context->buildViolation('sylius.promotion_coupon.expired')
                ->atPath('couponCode')
                ->addViolation()
            ;
        }
    }
}
