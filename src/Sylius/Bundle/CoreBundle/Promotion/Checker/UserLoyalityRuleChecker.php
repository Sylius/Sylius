<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Promotion\Checker;

use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;
use Sylius\Bundle\PromotionsBundle\Checker\RuleCheckerInterface;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\ResourceBundle\Exception\UnexpectedTypeException;
use DateTime;

/**
 * Checks if user is created before/after configured period of time.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class UserLoyalityRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Bundle\CoreBundle\Model\OrderInterface');
        }

        if (null === $user = $subject->getUser()) {
            return false;
        }

        $time = new DateTime(sprintf('%d %s ago', $configuration['time'], $configuration['unit']));

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
        return 'sylius_promotion_rule_user_loyality_configuration';
    }
}
