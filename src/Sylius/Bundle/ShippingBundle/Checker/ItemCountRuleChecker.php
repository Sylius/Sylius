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

use Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface;

/**
 * Checks if item count exceeds (or at least is equal) to the configured count.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemCountRuleChecker implements RuleCheckerInterface
{
    public function isEligible(ShippingSubjectInterface $subject, array $configuration)
    {
        $count = $subject->getShippingItemCount();

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
