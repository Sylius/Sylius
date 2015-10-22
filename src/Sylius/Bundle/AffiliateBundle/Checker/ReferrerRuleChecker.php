<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AffiliateBundle\Checker;

use Sylius\Component\Affiliate\Checker\RuleCheckerInterface;
use Sylius\Component\Core\Affiliate\AffiliateContextInterface;
use Symfony\Component\HttpFoundation\Request;

class ReferrerRuleChecker implements RuleCheckerInterface
{
    protected $affiliateContext;

    public function __construct(AffiliateContextInterface $affiliateContext)
    {
        $this->affiliateContext = $affiliateContext;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible($subject, array $configuration)
    {
        if (!$this->affiliateContext->hasAffiliate()) {
            return false;
        }

        return $this->affiliateContext->getAffiliate()->getId() == $configuration['affiliate'];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_affiliate_rule_referrer_configuration';
    }

    /**
     * {@inheritdoc}
     */
    public function supports($subject)
    {
        return true;
    }
}