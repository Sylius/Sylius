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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\CommandDispatcher;

use Sylius\Bundle\CoreBundle\PaymentRequest\CommandProvider\PaymentRequestCommandProviderInterface;
use Sylius\Bundle\PaymentBundle\Exception\PaymentRequestNotSupportedException;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class PaymentRequestCommandDispatcher implements PaymentRequestCommandDispatcherInterface
{
    public function __construct(
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private PaymentRequestCommandProviderInterface $paymentRequestCommandProvider,
        private MessageBusInterface $commandBus,
    ) {
    }

    public function add(PaymentRequestInterface $paymentRequest): void
    {
        $this->paymentRequestRepository->add($paymentRequest);

        $this->update($paymentRequest);
    }

    public function update(PaymentRequestInterface $paymentRequest): void
    {
        if (!$this->paymentRequestCommandProvider->supports($paymentRequest)) {
            throw new PaymentRequestNotSupportedException();
        }

        $command = $this->paymentRequestCommandProvider->provide($paymentRequest);

        $this->commandBus->dispatch($command);
    }
}
