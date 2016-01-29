<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Checker;

use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * Checks if subject’s total exceeds (or at least equal) to the configured amount.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemTotalRuleChecker implements RuleCheckerInterface
{
    use CountComparisonTrait;

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!isset($configuration['equal'])) {
            return static::comparison($subject->getPromotionSubjectTotal(), $configuration['amount']);
        }

        if (is_bool($configuration['equal'])) {
            if ($configuration['equal']) {
                return static::comparison($subject->getPromotionSubjectTotal(), $configuration['amount']);
            }

            return static::comparison($subject->getPromotionSubjectTotal(), $configuration['amount'], 'more_than');
        }

        return static::comparison($subject->getPromotionSubjectTotal(), $configuration['amount'], $configuration['equal']);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_item_total_configuration';
    }
}
