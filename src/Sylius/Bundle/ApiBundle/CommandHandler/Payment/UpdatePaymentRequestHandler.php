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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Payment;

use Sylius\Bundle\ApiBundle\Command\Payment\UpdatePaymentRequest;
use Sylius\Bundle\ApiBundle\Exception\PaymentRequestNotSupportedException;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestCommandProviderInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class UpdatePaymentRequestHandler implements MessageHandlerInterface
{
    public function __construct(
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private PaymentRequestCommandProviderInterface $paymentRequestCommandProvider,
        private MessageBusInterface $commandBus,
    ) {
    }

    public function __invoke(UpdatePaymentRequest $updatePaymentRequest): PaymentRequestInterface
    {
        $hash = $updatePaymentRequest->getHash();
        Assert::notNull($hash);

        $paymentRequest = $this->paymentRequestRepository->findOneByHash($hash);
        Assert::notNull($paymentRequest);

        $paymentRequest->setRequestPayload($updatePaymentRequest->getRequestPayload());

        if (!$this->paymentRequestCommandProvider->supports($paymentRequest)) {
            throw new PaymentRequestNotSupportedException();
        }

        $command = $this->paymentRequestCommandProvider->provide($paymentRequest);

        $this->commandBus->dispatch($command);

        return $paymentRequest;
    }
}
