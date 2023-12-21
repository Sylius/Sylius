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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Payment\Offline;

use Sylius\Bundle\ApiBundle\Applicator\PaymentStateMachineTransitionApplicatorInterface;
use Sylius\Bundle\ApiBundle\Command\Payment\Offline\CapturePaymentRequest;
use Sylius\Bundle\ApiBundle\Payment\Offline\PaymentRequestToDetailsConverterInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class CapturePaymentRequestHandler implements MessageHandlerInterface
{
    public const FACTORY_NAME = 'offline';

    public function __construct(
        private RepositoryInterface $paymentRequestRepository,
        private PaymentRequestToDetailsConverterInterface $paymentRequestToDetailsConverter,
        private PaymentStateMachineTransitionApplicatorInterface $paymentStateMachineTransitionApplicator,
    ) {
    }

    public function __invoke(CapturePaymentRequest $capturePaymentRequest): void
    {
        /** @var PaymentRequestInterface|null $paymentRequest */
        $paymentRequest = $this->paymentRequestRepository->find($capturePaymentRequest->getHash());
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

        $details = $this->paymentRequestToDetailsConverter->convert($paymentRequest);
        $paymentRequest->setResponseData($details);
        // @todo modify Payment->getDetails() to retrieve last payment request `responseData`

        if ($details['paid']) {
            $this->paymentStateMachineTransitionApplicator->complete($payment);
        } else {
            $this->paymentStateMachineTransitionApplicator->process($payment);
        }
    }
}
