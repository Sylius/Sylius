<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Taxation\Strategy;

use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Core\Taxation\Strategy\AbstractTaxCalculationStrategy;

/**
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
class TaxCalculationStrategy extends AbstractTaxCalculationStrategy
{
    /**
     * {@inheritdoc}
     */
    public function supports(OrderInterface $order, ZoneInterface $zone)
    {
        return $order->getChannel()->getDefaultTaxCalculationStrategy() === $this->type;
    }
}
