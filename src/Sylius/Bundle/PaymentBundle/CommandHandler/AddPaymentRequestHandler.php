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

namespace Sylius\Bundle\PaymentBundle\CommandHandler;

use Sylius\Bundle\PaymentBundle\Command\AddPaymentRequest;
use Sylius\Component\Payment\Factory\PaymentRequestFactoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class AddPaymentRequestHandler implements MessageHandlerInterface
{
    public function __construct(
        private RepositoryInterface $paymentMethodRepository,
        private RepositoryInterface $paymentRepository,
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
        Assert::notNull($paymentMethod, sprintf(
            'Payment method (code "%s") not found.',
            $addPaymentRequest->getPaymentMethodCode(),
        ));

        /** @var PaymentInterface|null $payment */
        $payment = $this->paymentRepository->find($addPaymentRequest->getPaymentId());
        Assert::notNull(
            $payment,
            sprintf('Payment (id "%s") not found.', $addPaymentRequest->getPaymentId()),
        );

        $paymentRequest = $this->paymentRequestFactory->create($payment, $paymentMethod);
        $paymentRequest->setAction($addPaymentRequest->getAction());
        $paymentRequest->setPayload($addPaymentRequest->getPayload());

        return $paymentRequest;
    }
}
