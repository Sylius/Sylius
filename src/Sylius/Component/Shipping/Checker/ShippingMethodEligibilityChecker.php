<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Checker;

use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Checker\Registry\RuleCheckerRegistryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

/**
 * Checks if shipping method rules are capable of shipping given subject.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShippingMethodEligibilityChecker implements ShippingMethodEligibilityCheckerInterface
{
    /**
     * Shipping rules registry.
     *
     * @var RuleCheckerRegistryInterface
     */
    protected $registry;

    /**
     * Constructor.
     *
     * @param \Sylius\Component\Shipping\Checker\Registry\RuleCheckerRegistryInterface $registry
     */
    public function __construct(RuleCheckerRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(ShippingSubjectInterface $subject, ShippingMethodInterface $method)
    {
        foreach ($method->getRules() as $rule) {
            $checker = $this->registry->getChecker($rule->getType());

            if (false === $checker->isEligible($subject, $rule->getConfiguration())) {
                return false;
            }
        }

        return true;
    }
}
