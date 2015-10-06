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


use Sylius\Component\Affiliate\Checker\RuleCheckerInterface;
use Sylius\Component\Core\Model\CustomerInterface;

class RegistrationRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible($subject, array $configuration)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_affiliate_rule_registration_configuration';
    }

    /**
     * {@inheritdoc}
     */
    public function supports($subject)
    {
        return $subject instanceof CustomerInterface;
    }
}