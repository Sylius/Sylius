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

use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\CustomerGroupConfigurationType;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerGroupRuleChecker implements RuleCheckerInterface
{
    const TYPE = 'customer_group';

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

        if (!$customer instanceof CustomerInterface) {
            return false;
        }

        if (null === $customer->getGroup()) {
            return false;
        }

        return $configuration['group_code'] === $customer->getGroup()->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return CustomerGroupConfigurationType::class;
    }
}
