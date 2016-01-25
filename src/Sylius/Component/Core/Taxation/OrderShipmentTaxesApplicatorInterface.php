<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Taxation;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
interface OrderShipmentTaxesApplicatorInterface
{
    /**
     * @param OrderInterface $order
     * @param TaxRateInterface $taxRate
     */
    public function apply(OrderInterface $order, TaxRateInterface $taxRate);
}
