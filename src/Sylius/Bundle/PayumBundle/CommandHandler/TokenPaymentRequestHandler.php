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

use Sylius\Bundle\PaymentBundle\Command\PaymentRequestHashAwareInterface;
use Sylius\Bundle\PayumBundle\Factory\TokenAggregateRequestFactoryInterface;
use Sylius\Bundle\PayumBundle\PaymentRequest\Factory\PayumTokenFactoryInterface;
use Sylius\Bundle\PayumBundle\PaymentRequest\Processor\AfterTokenRequestProcessorInterface;
use Sylius\Bundle\PayumBundle\PaymentRequest\Processor\RequestProcessorInterface;
use Sylius\Bundle\PayumBundle\PaymentRequest\Provider\PaymentRequestProviderInterface;
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
