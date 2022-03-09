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

final class PaymentFactory implements PaymentFactoryInterface
{
    public function __construct(private FactoryInterface $factory)
    {
    }

    public function createNew(): PaymentInterface
    {
        return $this->factory->createNew();
    }

    public function createWithAmountAndCurrencyCode(int $amount, string $currency): PaymentInterface
    {
        /** @var PaymentInterface $payment */
        $payment = $this->factory->createNew();
        $payment->setAmount($amount);
        $payment->setCurrencyCode($currency);

        return $payment;
    }
}
