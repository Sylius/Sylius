<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Affiliate\Checker;

use Sylius\Component\Affiliate\Checker\RuleCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

class ContainsProductRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible($subject, array $configuration)
    {
        /* @var $item OrderItemInterface */
        foreach ($subject->getItems() as $item) {
            if ($configuration['variant'] == $item->getVariant()->getId()) {
                return !$configuration['exclude'];
            }
        }

        return (bool) $configuration['exclude'];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_affiliate_rule_contains_product_configuration';
    }

    /**
     * {@inheritdoc}
     */
    public function supports($subject)
    {
        return $subject instanceof OrderInterface;
    }
}