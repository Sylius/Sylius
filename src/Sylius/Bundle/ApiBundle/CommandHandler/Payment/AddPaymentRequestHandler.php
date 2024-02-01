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
use Sylius\Bundle\ApiBundle\Exception\PaymentRequestNotSupportedException;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestCommandProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Factory\PaymentRequestFactoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class AddPaymentRequestHandler implements MessageHandlerInterface
{
    public function __construct(
        private PaymentRequestFactoryInterface $paymentRequestFactory,
        private PaymentMethodRepositoryInterface $paymentMethodRepository,
        private PaymentRepositoryInterface $paymentRepository,
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

        $command = $this->paymentRequestCommandProvider->provide($paymentRequest);

        $this->commandBus->dispatch($command);

        return $paymentRequest;
    }

    private function createPaymentRequest(AddPaymentRequest $addPaymentRequest): PaymentRequestInterface
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->findOneBy([
            'code' => $addPaymentRequest->getPaymentMethodCode()
        ]);
        Assert::notNull($paymentMethod, sprintf(
            'Payment method code "%s", can not be found!',
            $addPaymentRequest->getPaymentMethodCode()
        ));
        /** @var PaymentInterface|null $payment */
        $payment = $this->paymentRepository->find($addPaymentRequest->getPaymentId());
        Assert::notNull(
            $payment,
            sprintf('Payment ID "%s" can not be found!', $addPaymentRequest->getPaymentId())
        );

        $paymentRequest = $this->paymentRequestFactory->createWithPaymentAndPaymentMethod($payment, $paymentMethod);
        $paymentRequest->setType($addPaymentRequest->getType());
        $paymentRequest->setRequestPayload($addPaymentRequest->getRequestPayload());

        $this->paymentRepository->add($paymentRequest);
        return $paymentRequest;
    }
}
