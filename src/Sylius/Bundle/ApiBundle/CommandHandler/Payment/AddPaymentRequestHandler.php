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

use Sylius\Bundle\ApiBundle\Command\Payment\AddPaymentRequest;
use Sylius\Bundle\ApiBundle\Exception\PaymentMethodNotFoundException;
use Sylius\Bundle\ApiBundle\Exception\PaymentNotFoundException;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Factory\PaymentRequestFactoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/** @experimental */
final class AddPaymentRequestHandler implements MessageHandlerInterface
{
    public function __construct(
        private PaymentMethodRepositoryInterface $paymentMethodRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private PaymentRequestFactoryInterface $paymentRequestFactory,
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
    ) {
    }

    public function __invoke(AddPaymentRequest $addPaymentRequest): PaymentRequestInterface
    {
        $paymentRequest = $this->createPaymentRequest($addPaymentRequest);

        $this->paymentRequestRepository->add($paymentRequest);

        return $paymentRequest;
    }

    private function createPaymentRequest(AddPaymentRequest $addPaymentRequest): PaymentRequestInterface
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->findOneBy([
            'code' => $addPaymentRequest->getPaymentMethodCode(),
        ]);

        if (null === $paymentMethod) {
            throw new PaymentMethodNotFoundException();
        }

        /** @var PaymentInterface|null $payment */
        $payment = $this->paymentRepository->find($addPaymentRequest->getPaymentId());

        if (null === $payment) {
            throw new PaymentNotFoundException();
        }

        $paymentRequest = $this->paymentRequestFactory->create($payment, $paymentMethod);
        $paymentRequest->setAction($addPaymentRequest->getAction());
        $paymentRequest->setPayload($addPaymentRequest->getPayload());

        return $paymentRequest;
    }
}
