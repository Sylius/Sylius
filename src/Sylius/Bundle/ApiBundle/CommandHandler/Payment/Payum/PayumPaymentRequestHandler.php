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

use Payum\Core\Security\TokenAggregateInterface;
use Sylius\Bundle\ApiBundle\Command\PaymentRequestHashAwareInterface;
use Sylius\Bundle\ApiBundle\Payment\Payum\PayumRequestProcessorInterface;
use Sylius\Bundle\ApiBundle\Payment\Payum\PayumTokenFactoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class PayumPaymentRequestHandler implements MessageHandlerInterface
{
    public function __construct(
        private RepositoryInterface $paymentRequestRepository,
        private PayumTokenFactoryInterface $payumTokenFactory,
        private PayumRequestProcessorInterface $payumReplyProcessor,
        /** @var class-string<TokenAggregateInterface> */
        private string $payumRequestClass,
    ) {
    }

    public function __invoke(PaymentRequestHashAwareInterface $payumRequest): void
    {
        /** @var PaymentRequestInterface|null $paymentRequest */
        $paymentRequest = $this->paymentRequestRepository->find($payumRequest->getHash());
        Assert::notNull($paymentRequest);

        $token = $this->payumTokenFactory->createNew($paymentRequest);

        $captureRequest = new $this->payumRequestClass($token);
        $this->payumReplyProcessor->process($paymentRequest, $captureRequest);
    }
}
