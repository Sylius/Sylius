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

use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;

/**
 * Checks if subject item count exceeds (or at least equal) to the configured count.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemCountRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (isset($configuration['equal']) && $configuration['equal']) {
            return $subject->getPromotionSubjectItemCount() >= $configuration['count'];
        }

        return $subject->getPromotionSubjectItemCount() > $configuration['count'];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_item_count_configuration';
    }
}
