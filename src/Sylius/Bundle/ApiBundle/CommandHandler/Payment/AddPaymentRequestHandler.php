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
use Sylius\Bundle\CoreBundle\PaymentRequest\CommandDispatcher\PaymentRequestCommandDispatcherInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Factory\PaymentRequestFactoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class AddPaymentRequestHandler implements MessageHandlerInterface
{
    public function __construct(
        private PaymentRequestFactoryInterface $paymentRequestFactory,
        private PaymentMethodRepositoryInterface $paymentMethodRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private PaymentRequestCommandDispatcherInterface $paymentRequestCommandDispatcher,
    ) {
    }

    public function __invoke(AddPaymentRequest $addPaymentRequest): PaymentRequestInterface
    {
        $paymentRequest = $this->createPaymentRequest($addPaymentRequest);

        $this->paymentRequestCommandDispatcher->add($paymentRequest);

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

        $paymentRequest = $this->paymentRequestFactory->createWithPaymentAndPaymentMethod($payment, $paymentMethod);
        $paymentRequest->setAction($addPaymentRequest->getAction());
        $paymentRequest->setPayload($addPaymentRequest->getPayload());

        return $paymentRequest;
    }
}
