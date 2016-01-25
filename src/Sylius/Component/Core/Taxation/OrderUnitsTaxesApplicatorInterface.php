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

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
interface OrderUnitsTaxesApplicatorInterface
{
    /**
     * @param OrderItemInterface $item
     * @param TaxRateInterface $taxRate
     */
    public function apply(OrderItemInterface $item, TaxRateInterface $taxRate);
}
