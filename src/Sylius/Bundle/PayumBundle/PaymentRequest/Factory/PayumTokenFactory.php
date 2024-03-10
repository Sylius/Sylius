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

namespace Sylius\Bundle\PayumBundle\PaymentRequest\Factory;

use Payum\Core\Payum;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Webmozart\Assert\Assert;

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
        Assert::notNull($gatewayConfig, 'Gateway config cannot be null.');

        $gatewayName = $gatewayConfig->getGatewayName();

        /** @var array{
         *     'target_path'?: string,
         *     'target_path_parameters'?: array<string, string>,
         *     'after_path'?: string,
         *     'after_path_parameters'?: array<string, string>,
         * }|null $payload
         */
        $payload = $paymentRequest->getPayload();
        Assert::notNull($payload, 'The request payload need to be not null');

        $targetPath = $payload['target_path'] ?? null;
        Assert::notNull($targetPath, 'The request payload must have a "target_path" field not null');

        $targetPathParameters = $payload['target_path_parameters'] ?? [];

        $afterPath = $payload['after_path'] ?? null;
        Assert::notNull($afterPath, 'The request payload must have an "after_path" field not null');

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
