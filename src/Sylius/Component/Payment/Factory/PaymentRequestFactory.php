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
 * @implements PaymentRequestFactoryInterface<PaymentRequestInterface>
 */
final class PaymentRequestFactory implements PaymentRequestFactoryInterface
{
    public function __construct(private FactoryInterface $factory)
    {
    }

    public function createNew(): PaymentRequestInterface
    {
        return $this->factory->createNew();
    }

    public function createWithPaymentAndPaymentMethod(PaymentInterface $payment, PaymentMethodInterface $paymentMethod): PaymentRequestInterface
    {
        $paymentRequest = $this->createNew();

        $paymentRequest->setPayment($payment);
        $paymentRequest->setMethod($paymentMethod);

        return $paymentRequest;
    }

    public function createFromPaymentRequest(PaymentRequestInterface $paymentRequest): PaymentRequestInterface
    {
        $newPaymentRequest = $this->createNew();

        $newPaymentRequest->setPayment($paymentRequest->getPayment());
        $newPaymentRequest->setMethod($paymentRequest->getMethod());

        return $newPaymentRequest;
    }
}
