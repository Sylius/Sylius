<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OmnipayBundle\Model;

/**
 * Credit Card interface.
 *
 * @author Dylan Johnson <eponymi.dev@gmail.com>
 */
 interface CreditCardInterface
{
    /**
     * Map a credit card model's properties to an array with Omnipay
     * property names.
     *
     * @return array
     */
    public function mapToOmnipay(array $map);
}
