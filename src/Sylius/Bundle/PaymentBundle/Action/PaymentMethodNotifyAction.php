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

namespace Sylius\Bundle\PaymentBundle\Action;

use Sylius\Bundle\PaymentBundle\Announcer\PaymentRequestAnnouncerInterface;
use Sylius\Bundle\PaymentBundle\Processor\NotifyPayloadProcessorInterface;
use Sylius\Bundle\PaymentBundle\Provider\NotifyPaymentProviderInterface;
use Sylius\Bundle\PaymentBundle\Provider\NotifyResponseProviderInterface;
use Sylius\Component\Payment\Factory\PaymentRequestFactoryInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/** @experimental */
final class PaymentMethodNotifyAction
{
    /**
     * @param PaymentMethodRepositoryInterface<PaymentMethodInterface> $paymentMethodRepository
     * @param PaymentRequestFactoryInterface<PaymentRequestInterface> $paymentRequestFactory
     * @param PaymentRequestRepositoryInterface<PaymentRequestInterface> $paymentRequestRepository
     */
    public function __construct(
        private PaymentMethodRepositoryInterface $paymentMethodRepository,
        private NotifyPaymentProviderInterface $notifyPaymentProvider,
        private PaymentRequestFactoryInterface $paymentRequestFactory,
        private NotifyPayloadProcessorInterface $notifyPayloadProcessor,
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private PaymentRequestAnnouncerInterface $paymentRequestAnnouncer,
        private NotifyResponseProviderInterface $notifyResponseProvider,
    ) {
    }

    public function __invoke(Request $request, string $code): Response
    {
        $paymentMethod = $this->paymentMethodRepository->findOneBy([
            'code' => $code,
        ]);

        if (null === $paymentMethod) {
            throw new NotFoundHttpException(sprintf('No payment method found with code "%s".', $code));
        }

        $payment = $this->notifyPaymentProvider->getPayment($request, $paymentMethod);

        $paymentRequest = $this->paymentRequestFactory->create($payment, $paymentMethod);
        $paymentRequest->setAction(PaymentRequestInterface::ACTION_NOTIFY);

        $this->notifyPayloadProcessor->process($paymentRequest, $request);

        $this->paymentRequestRepository->add($paymentRequest);

        $this->paymentRequestAnnouncer->dispatchPaymentRequestCommand($paymentRequest);

        return $this->notifyResponseProvider->provide($paymentRequest);
    }
}
