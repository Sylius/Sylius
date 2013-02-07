<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Checker;

use Sylius\Bundle\SalesBundle\Model\OrderInterface;

/**
 * Checks if order item count exeeds (or at least equal) to the configured count.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemCountRuleChecker implements RuleCheckerInterface
{
    public function isEligible(OrderInterface $order, array $configuration)
    {
        if ($configuration['equal']) {
            return $order->countItems() >= $configuration['count']; }

        return $order->countItems() > $configuration['count'];
    }
}
