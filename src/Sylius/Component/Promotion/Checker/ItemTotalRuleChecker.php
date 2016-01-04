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
 * Checks if subject’s total is equal, exactly same, or more than to the configured amount.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class ItemTotalRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!isset($configuration['equal'])) {
            $configuration['equal'] = 'equal';
        } elseif (is_bool($configuration['equal'])) {
            $configuration['equal'] = $configuration['equal'] ? 'equal' : 'more_than';
        }

        $total = (int) $subject->getPromotionSubjectTotal();
        switch ($configuration['equal']) {
            default;
            case 'equal':
                $result = $total >= $configuration['amount'];

                break;

            case 'more_than':
                $result = $total > $configuration['amount'];

                break;

            case 'exactly':
                $result = $total === $configuration['amount'];

                break;
        }


        return (int) $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_item_total_configuration';
    }
}
