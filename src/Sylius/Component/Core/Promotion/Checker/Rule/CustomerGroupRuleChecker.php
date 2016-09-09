<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Checker\Rule;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\User\Model\GroupableInterface;
use Sylius\Component\User\Model\GroupInterface;

/**
 * @author Antonio Perić <antonio@locastic.com>
 */
class CustomerGroupRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnsupportedTypeException($subject, OrderInterface::class);
        }

        if (null === $customer = $subject->getCustomer()) {
            return false;
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
        return 'sylius_promotion_rule_customer_group_configuration';
    }
}
