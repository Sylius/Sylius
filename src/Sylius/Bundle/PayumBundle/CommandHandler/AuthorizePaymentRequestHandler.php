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

use Sylius\Bundle\PayumBundle\Command\AuthorizePaymentRequest;
use Sylius\Bundle\PayumBundle\Factory\AuthorizeRequestFactoryInterface;
use Sylius\Bundle\PayumBundle\PaymentRequest\Factory\PayumTokenFactoryInterface;
use Sylius\Bundle\PayumBundle\PaymentRequest\Processor\PayumRequestProcessorInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class AuthorizePaymentRequestHandler implements MessageHandlerInterface
{
    public function __construct(
        private RepositoryInterface $paymentRequestRepository,
        private PayumTokenFactoryInterface $payumTokenFactory,
        private PayumRequestProcessorInterface $payumReplyProcessor,
        private AuthorizeRequestFactoryInterface $factory,
    ) {
    }

    public function __invoke(AuthorizePaymentRequest $command): void
    {
        /** @var PaymentRequestInterface|null $paymentRequest */
        $paymentRequest = $this->paymentRequestRepository->find($command->getHash());
        Assert::notNull($paymentRequest);

        $token = $this->payumTokenFactory->createNew($paymentRequest);

        $request = $this->factory->createNewWithToken($token);

        $token = $request->getToken();
        Assert::notNull($token);

        $this->payumReplyProcessor->process($paymentRequest, $request, $token->getGatewayName());
    }
}
