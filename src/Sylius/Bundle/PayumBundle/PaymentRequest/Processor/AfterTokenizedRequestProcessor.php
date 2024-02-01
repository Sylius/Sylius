<?php

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\PaymentRequest\Processor;

use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\ApiBundle\Exception\PaymentRequestNotSupportedException;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestCommandProviderInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Factory\PaymentRequestFactoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class AfterTokenizedRequestProcessor implements AfterTokenizedRequestProcessorInterface
{
    public function __construct(
        private PaymentRequestFactoryInterface $paymentRequestFactory,
        private PaymentRequestCommandProviderInterface $paymentRequestCommandProvider,
        private PaymentRepositoryInterface $paymentRepository,
        private MessageBusInterface $commandBus,
    ) {
    }

    public function process(
        PaymentRequestInterface $paymentRequest,
        TokenInterface $token,
    ): void {
        if (PaymentRequestInterface::STATE_COMPLETED !== $paymentRequest->getState()) {
            return;
        }

        $details = $paymentRequest->getResponseData();
        $details['after_url'] = $token->getAfterUrl();
        $paymentRequest->setResponseData($details);

        $newPaymentRequest = $this->paymentRequestFactory->createFromPaymentRequest($paymentRequest);
        $newPaymentRequest->setType(PaymentRequestInterface::DATA_TYPE_STATUS);

        $this->paymentRepository->add($newPaymentRequest);

        if (!$this->paymentRequestCommandProvider->supports($newPaymentRequest)) {
            throw new PaymentRequestNotSupportedException();
        }

        $command = $this->paymentRequestCommandProvider->provide($newPaymentRequest);

        $this->commandBus->dispatch($command);
    }
}
