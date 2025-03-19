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

namespace Sylius\Bundle\PaymentBundle\Announcer;

use Sylius\Bundle\PaymentBundle\Checker\FinalizedPaymentRequestCheckerInterface;
use Sylius\Bundle\PaymentBundle\CommandProvider\PaymentRequestCommandProviderInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/** @experimental */
final readonly class PaymentRequestAnnouncer implements PaymentRequestAnnouncerInterface
{
    public function __construct(
        private FinalizedPaymentRequestCheckerInterface $finalizedPaymentRequestChecker,
        private PaymentRequestCommandProviderInterface $paymentRequestCommandProvider,
        private MessageBusInterface $commandBus,
    ) {
    }

    public function dispatchPaymentRequestCommand(PaymentRequestInterface $paymentRequest): void
    {
        if ($this->finalizedPaymentRequestChecker->isFinal($paymentRequest)) {
            return;
        }

        $this->commandBus->dispatch($this->paymentRequestCommandProvider->provide($paymentRequest));
    }
}
