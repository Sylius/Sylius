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
use Sylius\Component\Currency\Model\CurrencyAwareInterface;

class PercentageProvision extends AbstractProvision
{
    /**
     * {@inheritdoc}
     */
    public function execute($subject, array $configuration, AffiliateInterface $affiliate)
    {
        $adjustment = $this->createTransaction($affiliate);
        $adjustment->setAmount((int) round($subject->getTotal() * $configuration['percentage']));

        if ($subject instanceof CurrencyAwareInterface) {
            $adjustment->setCurrency($subject->getCurrency());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_affiliate_provision_percentage_configuration';
    }
}
