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
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @template T of PaymentRequestInterface
 *
 * @extends FactoryInterface<T>
 */
interface PaymentRequestFactoryInterface extends FactoryInterface
{
    public function createWithPaymentAndPaymentMethod(PaymentInterface $payment, PaymentMethodInterface $paymentMethod): PaymentRequestInterface;

    public function createFromPaymentRequest(PaymentRequestInterface $paymentRequest): PaymentRequestInterface;
}
