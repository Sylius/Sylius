<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;

interface AppliedCouponEligibilityCheckerInterface
{
    public function isEligible(?PromotionCouponInterface $promotionCoupon, OrderInterface $cart): bool;
}
