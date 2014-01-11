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
     * Transform any credit card model into an array matching Omnipay
     * format and naming conventions.
     *
     * @return array
     */
    public function transformToOmnipay(array $map);
}
