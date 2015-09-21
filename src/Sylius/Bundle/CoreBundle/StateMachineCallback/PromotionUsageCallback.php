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
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class PromotionUsageCallback
{
    /**
     * @param OrderInterface $order
     */
    public function incrementPromotionUsage(OrderInterface $order)
    {
        foreach ($order->getPromotions() as $promotion) {
            $promotion->incrementUsed();
        }
    }

    /**
     * @param OrderInterface $order
     */
    public function decrementPromotionUsage(OrderInterface $order)
    {
        foreach ($order->getPromotions() as $promotion) {
            $promotion->setUsed($promotion->getUsed() - 1);
        }
    }
}
