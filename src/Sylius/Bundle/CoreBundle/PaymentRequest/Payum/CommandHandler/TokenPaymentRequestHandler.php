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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Payum\CommandHandler;

use Sylius\Bundle\CoreBundle\PaymentRequest\Command\PaymentRequestHashAwareInterface;
use Sylius\Bundle\CoreBundle\PaymentRequest\Payum\Factory\PayumTokenFactoryInterface;
use Sylius\Bundle\CoreBundle\PaymentRequest\Payum\Processor\AfterTokenRequestProcessorInterface;
use Sylius\Bundle\CoreBundle\PaymentRequest\Payum\Processor\RequestProcessorInterface;
use Sylius\Bundle\CoreBundle\PaymentRequest\Payum\Provider\PaymentRequestProviderInterface;
use Sylius\Bundle\PayumBundle\Factory\TokenAggregateRequestFactoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class TokenPaymentRequestHandler implements MessageHandlerInterface
{
    public function __construct(
        private PaymentRequestProviderInterface $paymentRequestProvider,
        private PayumTokenFactoryInterface $payumTokenFactory,
        private RequestProcessorInterface $requestProcessor,
        private AfterTokenRequestProcessorInterface $afterTokenRequestProcessor,
        private TokenAggregateRequestFactoryInterface $payumRequestFactory,
    ) {
    }

    public function __invoke(PaymentRequestHashAwareInterface $command): void
    {
        $paymentRequest = $this->paymentRequestProvider->provideFromHash($command->getHash());
        Assert::notNull($paymentRequest);

        $token = $this->payumTokenFactory->createNew($paymentRequest);

        $request = $this->payumRequestFactory->createNewWithToken($token);

        $token = $request->getToken();
        Assert::notNull($token);

        $this->requestProcessor->process($paymentRequest, $request, $token->getGatewayName());

        $this->afterTokenRequestProcessor->process($paymentRequest, $token);
    }
}
