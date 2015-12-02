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
 * Checks if subject item count is: equal, exactly same, more than or "modulo", compared to the configured count.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class ItemCountRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof PromotionCountableSubjectInterface) {
            return 0;
        }

        if (!isset($configuration['equal'])) {
            $configuration['equal'] = 'equal';
        } elseif (is_bool($configuration['equal'])) {
            $configuration['equal'] = $configuration['equal'] ? 'equal' : 'more_than';
        }

        $quantity = (int) $subject->getPromotionSubjectCount();
        switch ($configuration['equal']) {
            default;
            case 'equal':
                $result = $quantity >= $configuration['count'];

                break;

            case 'more_than':
                $result = $quantity > $configuration['count'];

                break;

            case 'exactly':
                $result = $quantity === $configuration['count'];

                break;

            case 'modulo':
                $result = (int) floor($quantity / $configuration['count']);

                break;
        }

        return (int) $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_item_count_configuration';
    }
}
