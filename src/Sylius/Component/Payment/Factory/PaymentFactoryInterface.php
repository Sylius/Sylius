<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Payment\Factory;

use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface PaymentFactoryInterface extends FactoryInterface
{
    /**
     * @param int $amount
     * @param string $currency
     *
     * @return PaymentInterface
     */
    public function createWithAmountAndCurrencyCode(int $amount, string $currency): PaymentInterface;
}
