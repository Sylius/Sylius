<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Component\Affiliate\Checker;

class NthActionChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible($subject, array $configuration)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_affiliate_goal_rule_nth_action_configuration';
    }
}
