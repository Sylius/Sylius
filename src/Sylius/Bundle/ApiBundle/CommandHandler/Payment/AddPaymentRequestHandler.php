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

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\ApiBundle\Command\Payment\AddPaymentRequest;
use Sylius\Bundle\ApiBundle\Exception\PaymentRequestNotSupportedException;
use Sylius\Bundle\ApiBundle\Payment\PaymentRequestCommandProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class AddPaymentRequestHandler implements MessageHandlerInterface
{
    public function __construct(
        private FactoryInterface $paymentRequestFactory,
        private PaymentMethodRepositoryInterface $paymentMethodRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private ObjectManager $paymentRequestManager,
        private PaymentRequestCommandProviderInterface $paymentRequestCommandProvider,
        private MessageBusInterface $commandBus,
    ) {
    }

    public function __invoke(AddPaymentRequest $addPaymentRequest): PaymentRequestInterface
    {
        $paymentRequest = $this->createPaymentRequest($addPaymentRequest);
        if (!$this->paymentRequestCommandProvider->supports($paymentRequest)) {
            throw new PaymentRequestNotSupportedException();
        }

        $command = $this->paymentRequestCommandProvider->handle($paymentRequest);

        $this->commandBus->dispatch($command);

        return $paymentRequest;
    }

    private function createPaymentRequest(AddPaymentRequest $addPaymentRequest): PaymentRequestInterface
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->findOneBy(['code' => $addPaymentRequest->paymentMethodCode]);
        Assert::notNull($paymentMethod);
        /** @var PaymentInterface|null $payment */
        $payment = $this->paymentRepository->find($addPaymentRequest->paymentId);
        Assert::notNull($payment);

        /** @var PaymentRequestInterface $paymentRequest */
        $paymentRequest = $this->paymentRequestFactory->createNew();
        $paymentRequest->setPayment($payment);
        $paymentRequest->setMethod($paymentMethod);
        $paymentRequest->setType($addPaymentRequest->type);
        $paymentRequest->setPayload($addPaymentRequest->data);

        $this->paymentRequestManager->persist($paymentRequest);
        return $paymentRequest;
    }
}
