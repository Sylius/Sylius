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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Payum\Factory;

use Payum\Core\Payum;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Exception\InvalidPaymentRequestPayloadException;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class PayumTokenFactory implements PayumTokenFactoryInterface
{
    public function __construct(private Payum $payum)
    {
    }

    public function createNew(PaymentRequestInterface $paymentRequest): TokenInterface
    {
        $payment = $paymentRequest->getPayment();

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $paymentRequest->getMethod();

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        $gatewayName = $gatewayConfig->getGatewayName();

        /** @var array{
         *     'target_path'?: string,
         *     'target_path_parameters'?: array<string, string>,
         *     'after_path'?: string,
         *     'after_path_parameters'?: array<string, string>,
         * }|null $payload
         */
        $payload = $paymentRequest->getPayload();
        if (null === $payload) {
            throw new InvalidPaymentRequestPayloadException('Payload of the payment request cannot be null');
        }

        $targetPath = $payload['target_path'] ?? null;
        if (null === $targetPath) {
            throw new InvalidPaymentRequestPayloadException('The target path of the payment request cannot be null.');
        }

        $targetPathParameters = $payload['target_path_parameters'] ?? [];

        $afterPath = $payload['after_path'] ?? null;
        if (null === $afterPath) {
            throw new InvalidPaymentRequestPayloadException('The after path of the payment request cannot be null.');
        }

        $afterPathParameters = $payload['after_path_parameters'] ?? [];

        return $this->payum->getTokenFactory()->createToken(
            $gatewayName,
            $payment,
            $targetPath,
            $targetPathParameters,
            $afterPath,
            $afterPathParameters,
        );
    }
}
