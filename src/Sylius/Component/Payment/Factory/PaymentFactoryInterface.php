<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Payment\Factory;

use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @template T of PaymentInterface
 *
 * @extends FactoryInterface<T>
 */
interface PaymentFactoryInterface extends FactoryInterface
{
    public function createWithAmountAndCurrencyCode(int $amount, string $currency): PaymentInterface;
}
