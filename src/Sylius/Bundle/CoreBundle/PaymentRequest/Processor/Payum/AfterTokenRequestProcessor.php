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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Processor\Payum;

use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\CoreBundle\PaymentRequest\Announcer\PaymentRequestAnnouncerInterface;
use Sylius\Component\Payment\Factory\PaymentRequestFactoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;

final class AfterTokenRequestProcessor implements AfterTokenRequestProcessorInterface
{
    /**
     * @param PaymentRequestFactoryInterface<PaymentRequestInterface> $paymentRequestFactory
     * @param PaymentRequestRepositoryInterface<PaymentRequestInterface> $paymentRequestRepository
     */
    public function __construct(
        private PaymentRequestFactoryInterface $paymentRequestFactory,
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private PaymentRequestAnnouncerInterface $paymentRequestCommandDispatcher,
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
        $newPaymentRequest->setAction(PaymentRequestInterface::ACTION_STATUS);

        $this->paymentRequestRepository->add($newPaymentRequest);

        $this->paymentRequestCommandDispatcher->dispatchPaymentRequestCommand($newPaymentRequest);
    }
}
