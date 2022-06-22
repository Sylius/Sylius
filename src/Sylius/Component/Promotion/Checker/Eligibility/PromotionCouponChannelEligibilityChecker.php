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

namespace Sylius\Component\Promotion\Checker\Eligibility;

use Sylius\Component\Core\Model\OrderInterface as ModelOrderInterface;
use Sylius\Component\Core\Model\Promotion as CoreModelPromotion;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class PromotionCouponChannelEligibilityChecker implements PromotionCouponEligibilityCheckerInterface
{
    public function isEligible(PromotionSubjectInterface $promotionSubject, PromotionCouponInterface $promotionCoupon): bool
    {
        if (!$promotionSubject instanceof ModelOrderInterface) {
            return true;
        }

        $promotion = $promotionCoupon->getPromotion();


        if (!$promotion instanceof CoreModelPromotion) {
            return true;
        }

        $orderChannel = $promotionSubject->getChannel();

        if ($orderChannel === null) {
            return false;
        }

        return $promotion->getChannels()->contains($orderChannel);
    }
}
