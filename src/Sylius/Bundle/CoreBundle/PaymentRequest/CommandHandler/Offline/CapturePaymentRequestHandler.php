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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\CommandHandler\Offline;

use Sylius\Bundle\CoreBundle\PaymentRequest\Command\Offline\CapturePaymentRequest;
use Sylius\Bundle\CoreBundle\PaymentRequest\Processor\AfterOfflineCaptureProcessorInterface;
use Sylius\Bundle\CoreBundle\PaymentRequest\Processor\OfflineCaptureProcessorInterface;
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
        $hash = $capturePaymentRequest->getHash();
        Assert::notNull($hash, 'Payment request hash cannot be null.');

        $paymentRequest = $this->paymentRequestRepository->findOneByHash($hash);
        Assert::notNull($paymentRequest, sprintf('Payment request (hash "%s") not found.', $hash));

        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $paymentRequest->getMethod();
        Assert::notNull($paymentMethod, 'Payment cannot be null.');

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig, 'Payment method cannot be null.');
        $factoryName = $gatewayConfig->getConfig()['factory'] ?? $gatewayConfig->getFactoryName();
        Assert::eq($factoryName, self::FACTORY_NAME, 'Expected a factory name equal to %2$s. Got: %s');

        $payment = $paymentRequest->getPayment();
        Assert::notNull($payment);

        $this->offlineCaptureProcessor->process($paymentRequest);
        $this->afterOfflineCaptureProcessor->process($paymentRequest);
    }
}
