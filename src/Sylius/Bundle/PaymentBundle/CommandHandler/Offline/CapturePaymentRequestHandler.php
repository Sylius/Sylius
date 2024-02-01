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

namespace Sylius\Bundle\PaymentBundle\CommandHandler\Offline;

use Sylius\Bundle\PaymentBundle\Command\Offline\CapturePaymentRequest;
use Sylius\Bundle\PaymentBundle\Processor\AfterOfflineCaptureProcessorInterface;
use Sylius\Bundle\PaymentBundle\Processor\OfflineCaptureProcessorInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class CapturePaymentRequestHandler implements MessageHandlerInterface
{
    public const FACTORY_NAME = 'offline';

    public function __construct(
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private OfflineCaptureProcessorInterface $offlineCaptureProcessor,
        private AfterOfflineCaptureProcessorInterface $afterOfflineCaptureProcessor,
    ) {
    }

    public function __invoke(CapturePaymentRequest $capturePaymentRequest): void
    {
        $paymentRequest = $this->paymentRequestRepository->findOneByHash($capturePaymentRequest->getHash());
        Assert::notNull($paymentRequest);

        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $paymentRequest->getMethod();
        Assert::notNull($paymentMethod);

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig);
        $factoryName = $gatewayConfig->getConfig()['factory'] ?? $gatewayConfig->getFactoryName();
        Assert::eq($factoryName, self::FACTORY_NAME, 'Expected a factory name equal to %2$s. Got: %s');

        $payment = $paymentRequest->getPayment();
        Assert::notNull($payment);

        $this->offlineCaptureProcessor->process($paymentRequest);
        $this->afterOfflineCaptureProcessor->process($paymentRequest);
    }
}
