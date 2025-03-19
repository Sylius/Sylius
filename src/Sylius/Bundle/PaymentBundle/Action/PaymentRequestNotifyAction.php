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

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\PaymentBundle\Announcer\PaymentRequestAnnouncerInterface;
use Sylius\Bundle\PaymentBundle\Checker\FinalizedPaymentRequestCheckerInterface;
use Sylius\Bundle\PaymentBundle\Processor\NotifyPayloadProcessorInterface;
use Sylius\Bundle\PaymentBundle\Provider\NotifyResponseProviderInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/** @experimental */
final class PaymentRequestNotifyAction
{
    /**
     * @param PaymentRequestRepositoryInterface<PaymentRequestInterface> $paymentRequestRepository
     */
    public function __construct(
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private FinalizedPaymentRequestCheckerInterface $finalizedPaymentRequestChecker,
        private NotifyPayloadProcessorInterface $notifyPayloadProcessor,
        private EntityManagerInterface $paymentRequestManager,
        private PaymentRequestAnnouncerInterface $paymentRequestAnnouncer,
        private NotifyResponseProviderInterface $notifyResponseProvider,
    ) {
    }

    public function __invoke(Request $request, string $hash): Response
    {
        $paymentRequest = $this->paymentRequestRepository->find($hash);

        if (null === $paymentRequest) {
            throw new NotFoundHttpException(sprintf('No payment request found with hash "%s".', $hash));
        }

        if ($this->finalizedPaymentRequestChecker->isFinal($paymentRequest)) {
            throw new NotFoundHttpException(sprintf('The payment request with hash "%s" is on a final state (state: %s).', $hash, $paymentRequest->getState()));
        }

        $this->notifyPayloadProcessor->process($paymentRequest, $request);

        $this->paymentRequestManager->flush();

        $this->paymentRequestAnnouncer->dispatchPaymentRequestCommand($paymentRequest);

        return $this->notifyResponseProvider->provide($paymentRequest);
    }
}
