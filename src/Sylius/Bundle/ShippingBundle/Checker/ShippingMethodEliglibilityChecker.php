<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Checker;

use Sylius\Bundle\ShippingBundle\Checker\Registry\RuleCheckerRegistryInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippablesAwareInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface;

/**
 * Checks if shipping method rules are eligible.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShippingMethodEliglibilityChecker implements ShippingMethodEliglibilityCheckerInterface
{
    protected $registry;

    public function __construct(RuleCheckerRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function isEligible(ShippablesAwareInterface $shippablesAware, ShippingMethodInterface $shippingMethod)
    {
        foreach ($shippingMethod->getRules() as $rule) {
            $checker = $this->registry->getChecker($rule->getType());

            if (false === $checker->isEligible($shippablesAware, $rule->getConfiguration())) {
                return false;
            }
        }

        return true;
    }
}
