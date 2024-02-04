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
use Sylius\Bundle\CoreBundle\PaymentRequest\CommandDispatcher\PaymentRequestCommandDispatcherInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class UpdatePaymentRequestHandler implements MessageHandlerInterface
{
    public function __construct(
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private PaymentRequestCommandDispatcherInterface $paymentRequestCommandDispatcher,
    ) {
    }

    public function __invoke(UpdatePaymentRequest $updatePaymentRequest): PaymentRequestInterface
    {
        $hash = $updatePaymentRequest->getHash();
        Assert::notNull($hash);

        $paymentRequest = $this->paymentRequestRepository->findOneByHash($hash);
        Assert::notNull($paymentRequest);

        $paymentRequest->setRequestPayload($updatePaymentRequest->getRequestPayload());

        $this->paymentRequestCommandDispatcher->add($paymentRequest);

        return $paymentRequest;
    }
}
