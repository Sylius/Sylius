<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\StateMachineCallback;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Model\CouponInterface;

/**
 * Increments (or decrements) coupon usage when a coupon is used by an order.
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class CouponUsageCallback
{
    /**
     * @var OriginatorInterface
     */
    protected $originator;

    public function __construct(OriginatorInterface $originator)
    {
        $this->originator = $originator;
    }

    public function incrementCouponUsage(OrderInterface $order)
    {
        foreach ($order->getPromotionCoupons() as $coupon) {
            $coupon->incrementUsed();

            if (CouponInterface::TYPE_GIFT_CARD === $coupon->getType()) {
                $amount = $coupon->getAmount() - $order->getTotal();
                $coupon->setAmount($amount < 0 ? 0 : $coupon->getAmount() + $order->getAdjustmentsTotal(AdjustmentInterface::PROMOTION_ADJUSTMENT));
            }
        }
    }

    public function decrementCouponUsage(OrderInterface $order)
    {
        foreach ($order->getPromotionCoupons() as $coupon) {
            $coupon->decrementUsed();

            if (CouponInterface::TYPE_GIFT_CARD === $coupon->getType()) {
                if (0 === $coupon->getAmount()) {
                    $amount = $order->getAdjustmentsTotal(AdjustmentInterface::PROMOTION_ADJUSTMENT);
                } else {
                    $amount = $order->getAdjustmentsTotal(AdjustmentInterface::PROMOTION_ADJUSTMENT) + $coupon->getAmount();
                }

                $coupon->setAmount($amount);
            }
        }
    }
}
