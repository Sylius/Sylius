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

use Sylius\Bundle\PromotionsBundle\Checker\Registry\RuleCheckerRegistryInterface;
use Sylius\Bundle\SalesBundle\Model\OrderInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;

/**
 * Checks if promotion rules are eligible.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionEliglibilityChecker implements PromotionEliglibilityCheckerInterface
{
    protected $registry;

    public function __construct(RuleCheckerRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function isEligible(OrderInterface $order, PromotionInterface $promotion)
    {
        foreach ($promotion->getRules() as $rule) {
            $checker = $this->registry->getChecker($rule->getType());

            if (false === $checker->isEligible($order, $rule->getConfiguration())) {
                return false;
            }
        }

        return true;
    }
}
