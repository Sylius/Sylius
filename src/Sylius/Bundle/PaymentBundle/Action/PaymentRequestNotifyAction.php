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
use Sylius\Bundle\PaymentBundle\Wrapper\SymfonyRequestWrapperInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PaymentRequestNotifyAction
{
    /**
     * @param PaymentRequestRepositoryInterface<PaymentRequestInterface> $paymentRequestRepository
     */
    public function __construct(
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private SymfonyRequestWrapperInterface $requestWrapper,
        private PaymentRequestAnnouncerInterface $paymentRequestAnnouncer,
        private EntityManagerInterface $paymentRequestManager
    ) {
    }

    public function __invoke(Request $request, string $hash): Response
    {
        $paymentRequest = $this->paymentRequestRepository->findOneBy([
            'hash' => $hash,
        ]);

        if (null === $paymentRequest) {
            throw new NotFoundHttpException(sprintf('No payment request found with hash "%s".', $hash));
        }

        if (PaymentRequestInterface::STATE_COMPLETED === $paymentRequest->getState()) {
            throw new NotFoundHttpException(sprintf('The payment request with hash "%s" is already completed.', $hash));
        }

        $payload = $this->requestWrapper->wrap($request);
        $currentPayload = $paymentRequest->getPayload();
        if (is_array($currentPayload)) {
            $payload += $currentPayload;
        }

        $paymentRequest->setPayload($payload);

        $this->paymentRequestManager->flush();

        $this->paymentRequestAnnouncer->dispatchPaymentRequestCommand($paymentRequest);

        return new Response('', 204);
    }
}
