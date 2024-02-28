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
use Sylius\Component\Resource\Exception\UnsupportedMethodException;

/**
 * @implements PaymentRequestFactoryInterface<PaymentRequestInterface>
 */
final class PaymentRequestFactory implements PaymentRequestFactoryInterface
{
    public function __construct(private string $className)
    {
        if (!is_a($className, PaymentRequestInterface::class, true)) {
            throw new \DomainException(sprintf(
                'This factory requires %s or its descend to be used as resource',
                PaymentRequestInterface::class,
            ));
        }
    }

    /**
     * @throws UnsupportedMethodException
     */
    public function createNew(): object
    {
        throw new UnsupportedMethodException('createNew');
    }

    public function create(PaymentInterface $payment, PaymentMethodInterface $paymentMethod): PaymentRequestInterface
    {
        return new $this->className($payment, $paymentMethod);
    }

    public function createFromPaymentRequest(PaymentRequestInterface $paymentRequest): PaymentRequestInterface
    {
        return $this->create($paymentRequest->getPayment(), $paymentRequest->getMethod());
    }
}
