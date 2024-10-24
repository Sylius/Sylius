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
use Sylius\Bundle\PaymentBundle\Provider\DefaultActionProviderInterface;
use Sylius\Bundle\PaymentBundle\Provider\DefaultPayloadProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Factory\PaymentRequestFactoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/** @experimental */
#[AsMessageHandler]
final class AddPaymentRequestHandler
{
    /**
     * @param PaymentMethodRepositoryInterface<PaymentMethodInterface> $paymentMethodRepository
     * @param PaymentRepositoryInterface<PaymentInterface> $paymentRepository
     * @param PaymentRequestFactoryInterface<PaymentRequestInterface> $paymentRequestFactory
     * @param PaymentRequestRepositoryInterface<PaymentRequestInterface> $paymentRequestRepository
     */
    public function __construct(
        private PaymentMethodRepositoryInterface $paymentMethodRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private PaymentRequestFactoryInterface $paymentRequestFactory,
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private DefaultActionProviderInterface $defaultActionProvider,
        private DefaultPayloadProviderInterface $defaultPayloadProvider,
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
        /** @var PaymentInterface|null $payment */
        $payment = $this->paymentRepository->findOneByOrderToken($addPaymentRequest->paymentId, $addPaymentRequest->orderTokenValue);
        if (null === $payment) {
            throw new PaymentNotFoundException();
        }

        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->findOneBy(['code' => $addPaymentRequest->paymentMethodCode]);
        if (null === $paymentMethod) {
            throw new PaymentMethodNotFoundException();
        }

        $paymentRequest = $this->paymentRequestFactory->create($payment, $paymentMethod);
        $paymentRequest->setAction($addPaymentRequest->action ?? $this->defaultActionProvider->getAction($paymentRequest));
        $paymentRequest->setPayload($addPaymentRequest->payload ?? $this->defaultPayloadProvider->getPayload($paymentRequest));

        return $paymentRequest;
    }
}
