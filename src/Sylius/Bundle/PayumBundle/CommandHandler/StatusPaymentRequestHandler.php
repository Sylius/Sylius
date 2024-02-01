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

namespace Sylius\Bundle\PayumBundle\CommandHandler;

use Sylius\Bundle\PayumBundle\Command\StatusPaymentRequest;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use Sylius\Bundle\PayumBundle\PaymentRequest\Processor\RequestProcessorInterface;
use Sylius\Bundle\PayumBundle\PaymentRequest\Provider\PaymentRequestProviderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class StatusPaymentRequestHandler implements MessageHandlerInterface
{
    public function __construct(
        private PaymentRequestProviderInterface $paymentRequestProvider,
        private RequestProcessorInterface $requestProcessor,
        private GetStatusFactoryInterface $factory,
    ) {
    }

    public function __invoke(StatusPaymentRequest $command): void
    {
        $paymentRequest = $this->paymentRequestProvider->provideFromHash($command->getHash());
        Assert::notNull($paymentRequest);

        $payment = $paymentRequest->getPayment();
        Assert::notNull($payment);

        $request = $this->factory->createNewWithModel($payment);

        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $paymentRequest->getMethod();
        Assert::notNull($paymentMethod);

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig);

        $gatewayName = $gatewayConfig->getGatewayName();

        $this->requestProcessor->process($paymentRequest, $request, $gatewayName);
    }
}
