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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\CommandHandler\Payum;

use Sylius\Bundle\CoreBundle\PaymentRequest\Command\PaymentRequestHashAwareInterface;
use Sylius\Bundle\CoreBundle\PaymentRequest\Payum\Provider\PaymentRequestProviderInterface;
use Sylius\Bundle\CoreBundle\PaymentRequest\Processor\Payum\RequestProcessorInterface;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class ModelPaymentRequestHandler implements MessageHandlerInterface
{
    public function __construct(
        private PaymentRequestProviderInterface $paymentRequestProvider,
        private RequestProcessorInterface $requestProcessor,
        private GetStatusFactoryInterface $factory,
    ) {
    }

    public function __invoke(PaymentRequestHashAwareInterface $command): void
    {
        $hash = $command->getHash();
        Assert::notNull($hash, 'Payment request hash cannot be null.');

        $paymentRequest = $this->paymentRequestProvider->provideFromHash($hash);
        Assert::notNull($paymentRequest, sprintf('Payment request (hash "%s") not found.', $hash));

        $payment = $paymentRequest->getPayment();
        Assert::notNull($payment, 'Payment cannot be null.');

        $request = $this->factory->createNewWithModel($payment);

        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $paymentRequest->getMethod();
        Assert::notNull($paymentMethod, 'Payment method cannot be null.');

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig, 'Gateway config cannot be null.');

        $gatewayName = $gatewayConfig->getGatewayName();

        $this->requestProcessor->process($paymentRequest, $request, $gatewayName);
    }
}
