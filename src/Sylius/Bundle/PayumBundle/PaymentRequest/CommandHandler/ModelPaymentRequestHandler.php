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

namespace Sylius\Bundle\PayumBundle\PaymentRequest\CommandHandler;

use Sylius\Bundle\PaymentBundle\Command\PaymentRequestHashAwareInterface;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestProviderInterface;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Bundle\PayumBundle\PaymentRequest\Processor\RequestProcessorInterface;
use Sylius\Bundle\PayumBundle\PaymentRequest\Resolver\DoctrineProxyObjectResolverInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/** @experimental */
#[AsMessageHandler]
final class ModelPaymentRequestHandler
{
    public function __construct(
        private PaymentRequestProviderInterface $paymentRequestProvider,
        private DoctrineProxyObjectResolverInterface $doctrineProxyObjectResolver,
        private RequestProcessorInterface $requestProcessor,
        private GetStatusFactoryInterface $factory,
    ) {
    }

    public function __invoke(PaymentRequestHashAwareInterface $command): void
    {
        $paymentRequest = $this->paymentRequestProvider->provide($command);
        $this->doctrineProxyObjectResolver->resolve($paymentRequest);

        $payment = $paymentRequest->getPayment();
        $request = $this->factory->createNewWithModel($payment);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $paymentRequest->getMethod();

        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();

        $this->requestProcessor->process($paymentRequest, $request, $gatewayConfig->getGatewayName());
    }
}
