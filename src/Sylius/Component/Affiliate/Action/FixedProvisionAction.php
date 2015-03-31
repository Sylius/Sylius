<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Action;

use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Currency\Model\CurrencyAwareInterface;

class FixedProvisionAction extends ProvisionAction
{
    /**
     * {@inheritdoc}
     */
    public function execute($subject, array $configuration, AffiliateInterface $affiliate)
    {
        $adjustment = $this->createTransaction($affiliate);
        $adjustment->setAmount(- $configuration['amount']);

        if ($subject instanceof CurrencyAwareInterface) {
            $adjustment->setCurrency($subject->getCurrency());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_affiliate_action_fixed_provision_configuration';
    }
}
