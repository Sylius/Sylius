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

        $paymentRequest->setPayload($request);

        $this->paymentRequestManager->flush();

        $this->paymentRequestAnnouncer->dispatchPaymentRequestCommand($paymentRequest);

        return new Response('', 204);
    }
}
