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

use Sylius\Bundle\ShippingBundle\Model\ShippablesAwareInterface;

/**
 * Checks if shippables count exeeds (or at least equal) to the configured count.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemCountRuleChecker implements RuleCheckerInterface
{
    public function isEligible(ShippablesAwareInterface $shippablesAware, array $configuration)
    {
        $count = $shippablesAware->getShippables()->count();

        if ($configuration['equal']) {
            return $count >= $configuration['count'];
        }

        return $count > $configuration['count'];
    }

    public function getConfigurationFormType()
    {
        return 'sylius_shipping_rule_item_count_configuration';
    }
}
