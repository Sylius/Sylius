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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Payment\Payum;

use Payum\Core\Payum;
use Payum\Core\Request\Capture;
use Sylius\Bundle\ApiBundle\Command\Payment\Payum\PayumCapture;
use Sylius\Bundle\ApiBundle\Payment\Payum\PayumRequestProcessorInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class PayumCaptureHandler implements MessageHandlerInterface
{
    public function __construct(
        private RepositoryInterface $paymentRequestRepository,
        private Payum $payum,
        private PayumRequestProcessorInterface $payumReplyProcessor,
    ) {
    }

    public function __invoke(PayumCapture $payumCapture): void
    {
        /** @var PaymentRequestInterface|null $paymentRequest */
        $paymentRequest = $this->paymentRequestRepository->find($payumCapture->hash);
        Assert::notNull($paymentRequest);

        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $paymentRequest->getMethod();
        Assert::notNull($paymentMethod);

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig);

        $gatewayName = $gatewayConfig->getGatewayName();

        $payment = $paymentRequest->getPayment();
        Assert::notNull($payment);

        /** @var array|null $data */
        $data = $paymentRequest->getData();
        Assert::notNull($data);

        $afterPath = $data['after_path'] ?? null;
        Assert::notNull($afterPath);

        $afterPathParameters = $data['after_path_parameters'] ?? [];

        $token = $this->payum->getTokenFactory()->createCaptureToken(
            $gatewayName,
            $payment,
            $afterPath,
            $afterPathParameters
        );

        $captureRequest = new Capture($token);
        $this->payumReplyProcessor->process($paymentRequest, $captureRequest);
    }
}
