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
 * Checks if subject items quantity exceeds (or at least equal) to the configured count.
 *
 * @author Krzysztof Wędrowicz <krzysztof@wedrowicz.me>
 */
class ItemsQuantityRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof PromotionCountableSubjectInterface) {
            return false;
        }

        if (isset($configuration['equal']) && $configuration['equal']) {
            return $subject->getPromotionSubjectQuantity() >= $configuration['count'];
        }

        return $subject->getPromotionSubjectQuantity() > $configuration['count'];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_item_count_configuration';
    }
}
