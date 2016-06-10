<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Payment\Factory;

use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface PaymentFactoryInterface extends FactoryInterface
{
    /**
     * @param int $amount
     * @param string $currency
     *
     * @return PaymentInterface
     */
    public function createWithAmountAndCurrencyCode($amount, $currency);
}
