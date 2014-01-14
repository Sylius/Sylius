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
use Sylius\Bundle\ResourceBundle\Checker\RuleCheckerInterface;
use Sylius\Bundle\ResourceBundle\Model\SubjectInterface;

/**
 * Checks if subject’s total exceeds (or at least equal) to the configured amount.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemTotalRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(SubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof PromotionSubjectInterface) {
            throw new \InvalidArgumentException();
        }

        if (isset($configuration['equal']) && $configuration['equal']) {
            return $subject->getPromotionSubjectItemTotal() >= $configuration['amount'];
        }

        return $subject->getPromotionSubjectItemTotal() > $configuration['amount'];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_item_total_configuration';
    }
}
