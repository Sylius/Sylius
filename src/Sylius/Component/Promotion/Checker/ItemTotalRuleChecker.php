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
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemTotalRuleChecker implements RuleCheckerInterface
{
    const TYPE = 'item_total';

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        return $subject->getPromotionSubjectTotal() >= $configuration['amount'];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_item_total_configuration';
    }
}
