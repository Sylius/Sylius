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
use Symfony\Component\HttpFoundation\Request;

class UriVisitRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible($subject, array $configuration)
    {
        /**
         * @var Request $subject
         */
        return $configuration['uri'] === $subject->getUri();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_affiliate_rule_uri_visit_configuration';
    }

    /**
     * {@inheritdoc}
     */
    public function supports($subject)
    {
        return $subject instanceof Request;
    }
}