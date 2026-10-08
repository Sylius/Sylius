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

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Webmozart\Assert\Assert;

final class OrderPromotionCouponProcessor implements OrderProcessorInterface
{
    public function __construct(private PromotionCouponEligibilityCheckerInterface $promotionCouponEligibilityChecker)
    {
    }

    public function process(BaseOrderInterface $order): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        if (!$order->canBeProcessed()) {
            return;
        }

        $promotionCoupon = $order->getPromotionCoupon();
        if (null === $promotionCoupon) {
            return;
        }

        if ($this->promotionCouponEligibilityChecker->isEligible($order, $promotionCoupon)) {
            return;
        }

        $order->setPromotionCoupon(null);
    }
}
