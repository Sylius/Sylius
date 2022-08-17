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

namespace Sylius\Component\Core\Checker\Eligibility;

use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Webmozart\Assert\Assert;

final class PromotionCouponChannelEligibilityChecker implements PromotionCouponEligibilityCheckerInterface
{
    public function isEligible(PromotionSubjectInterface $promotionSubject, PromotionCouponInterface $promotionCoupon): bool
    {
        Assert::isInstanceOf($promotionSubject, ChannelAwareInterface::class);
        $orderChannel = $promotionSubject->getChannel();
        Assert::notNull($orderChannel);

        $promotion = $promotionCoupon->getPromotion();
        Assert::isInstanceOf($promotion, PromotionInterface::class);

        return $promotion->hasChannel($orderChannel);
    }
}
