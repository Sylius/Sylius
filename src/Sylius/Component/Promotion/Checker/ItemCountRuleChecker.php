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
use Sylius\Component\Promotion\Model\PromotionCountableSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

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
        if (!$subject instanceof PromotionCountableSubjectInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Component\Promotion\Model\PromotionCountableSubjectInterface');
        }

        if (isset($configuration['equal']) && $configuration['equal']) {
            return $subject->getPromotionSubjectCount() >= $configuration['count'];
        }

        return $subject->getPromotionSubjectCount() > $configuration['count'];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_item_count_configuration';
    }
}
