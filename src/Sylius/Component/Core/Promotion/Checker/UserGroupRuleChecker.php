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
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Core\Model\GroupInterface;
use FOS\UserBundle\Model\User;

/**
 * Checks if user is part of Group.
 *
 * @author Antonio Perić <antonio@locastic.com>
 */
class UserGroupRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnsupportedTypeException($subject, 'Sylius\Component\Core\Model\OrderInterface');
        }

        /* @var $user User */
        if (null === $user = $subject->getUser()) {
            return false;
        }

        /* @var $group GroupInterface */
        foreach ($user->getGroups() as $group) {
            if ($configuration['groups'] == $group->getId()) {

                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_user_group_configuration';
    }
}
