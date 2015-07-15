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

use Sylius\Component\Core\Model\OrderInterface;

/**
 * Increments promotion usage when a promotion is used by an order
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class PromotionUsageCallback
{
    public function incrementPromotionUsage(OrderInterface $order)
    {
        foreach ($order->getPromotions() as $promotion) {
            $promotion->incrementUsed();
        }
    }

    public function decrementPromotionUsage(OrderInterface $order)
    {
        foreach ($order->getPromotions() as $promotion) {
            $promotion->setUsed($promotion->getUsed() - 1);
        }
    }
}
