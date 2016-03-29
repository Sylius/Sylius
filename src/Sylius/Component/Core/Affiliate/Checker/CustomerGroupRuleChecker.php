<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Affiliate\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Affiliate\Checker\RuleCheckerInterface;
use Sylius\Component\User\Model\CustomerInterface;
use Sylius\Component\User\Model\GroupableInterface;
use Sylius\Component\User\Model\GroupInterface;

/**
 * Checks if customer is part of Group.
 *
 * @author Antonio Perić <antonio@locastic.com>
 */
class CustomerGroupRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible($subject, array $configuration)
    {
        if ($subject instanceof OrderInterface) {
            $customer = $subject->getCustomer();
        } elseif ($subject instanceof CustomerInterface) {
            $customer = $subject;
        }

        if (!$customer instanceof GroupableInterface) {
            return false;
        }

        /* @var GroupInterface $group */
        foreach ($customer->getGroups() as $group) {
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
        return 'sylius_affiliate_rule_customer_group_configuration';
    }

    /**
     * {@inheritdoc}
     */
    public function supports($subject)
    {
        return ($subject instanceof OrderInterface
            || $subject instanceof CustomerInterface);
    }
}
