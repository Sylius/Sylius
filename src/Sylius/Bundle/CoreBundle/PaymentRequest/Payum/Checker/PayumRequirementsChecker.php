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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Payum\Checker;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Proxy;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class PayumRequirementsChecker implements PayumRequirementsCheckerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function check(PaymentRequestInterface $paymentRequest): void
    {
        // Needed to get a real object to give to Payum which is not handling
        // Proxy class from Doctrine when a token is created for ex.
        $payment = $paymentRequest->getPayment();
        if ($payment instanceof Proxy) {
            $this->entityManager->detach($payment);
            $payment = $this->entityManager->find(PaymentInterface::class, $payment->getId());
            $paymentRequest->setPayment($payment);
        }
    }
}
