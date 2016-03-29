<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Provision;

use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Affiliate\Model\ProvisionInterface;

class PercentageProvision extends AbstractProvision
{
    /**
     * {@inheritdoc}
     */
    public function apply($subject, ProvisionInterface $provision, AffiliateInterface $affiliate)
    {
        $configuration = $provision->getConfiguration();

        $adjustment = $this->createTransaction($affiliate, $provision->getGoal());
        $adjustment->setAmount((int) round($subject->getTotal() * $configuration['percentage']));
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_affiliate_provision_percentage_configuration';
    }
}
