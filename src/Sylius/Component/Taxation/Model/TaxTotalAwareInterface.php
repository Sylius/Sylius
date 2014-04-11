<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxation\Model;

interface TaxTotalAwareInterface
{
    /**
     * Get the tax total.
     *
     * @return float
     */
    public function getTaxTotal();
}
