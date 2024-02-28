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

namespace Sylius\Bundle\ApiBundle\Payment\Payum;

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
        Assert::notNull($payment);

        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $paymentRequest->getMethod();
        Assert::notNull($paymentMethod);

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig);

        $gatewayName = $gatewayConfig->getGatewayName();

        /** @var array|null $data */
        $data = $paymentRequest->getData();
        Assert::notNull($data);

        $targetPath = $data['target_path'] ?? null;
        Assert::notNull($targetPath);

        $targetPathParameters = $data['target_path_parameters'] ?? [];

        $afterPath = $data['after_path'] ?? null;
        Assert::notNull($afterPath);

        $afterPathParameters = $data['after_path_parameters'] ?? [];

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
