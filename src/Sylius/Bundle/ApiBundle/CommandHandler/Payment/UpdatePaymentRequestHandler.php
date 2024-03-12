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
use Sylius\Component\Payment\Exception\PaymentRequestNotFoundException;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/** @experimental */
final class UpdatePaymentRequestHandler implements MessageHandlerInterface
{
    /**
     * @param PaymentRequestRepositoryInterface<PaymentRequestInterface> $paymentRequestRepository
     */
    public function __construct(
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
    ) {
    }

    public function __invoke(UpdatePaymentRequest $updatePaymentRequest): PaymentRequestInterface
    {
        $hash = $updatePaymentRequest->getHash();

        $paymentRequest = $this->paymentRequestRepository->findOneByHash($hash);
        if (null === $paymentRequest) {
            throw new PaymentRequestNotFoundException();
        }

        $paymentRequest->setPayload($updatePaymentRequest->getPayload());

        return $paymentRequest;
    }
}
