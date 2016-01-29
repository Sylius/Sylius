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

use Sylius\Component\Promotion\Model\PromotionCountableSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * Checks if subject item count exceeds (or at least equal) to the configured count.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemCountRuleChecker implements RuleCheckerInterface
{
    use CountComparisonTrait;

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof PromotionCountableSubjectInterface) {
            return false;
        }

        if (!isset($configuration['equal'])) {
            return static::comparison($subject->getPromotionSubjectCount(), $configuration['count']);
        }

        if (is_bool($configuration['equal'])) {
            if ($configuration['equal']) {
                return static::comparison($subject->getPromotionSubjectCount(), $configuration['count']);
            }

            return static::comparison($subject->getPromotionSubjectCount(), $configuration['count'], 'more_than');
        }

        return static::comparison($subject->getPromotionSubjectCount(), $configuration['count'], $configuration['equal']);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_item_count_configuration';
    }
}
