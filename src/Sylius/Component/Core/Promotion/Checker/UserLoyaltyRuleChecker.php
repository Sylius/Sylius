<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * Checks if user is created before/after configured period of time.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class UserLoyaltyRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            return false;
        }

        if (null === $user = $subject->getUser()) {
            return false;
        }

        $time = new \DateTime(sprintf('%d %s ago', $configuration['time'], $configuration['unit']));

        if (isset($configuration['after']) && $configuration['after']) {
            return $user->getCreatedAt() >= $time;
        }

        return $user->getCreatedAt() < $time;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_user_loyalty_configuration';
    }
}
