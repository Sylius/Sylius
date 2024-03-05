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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Payum\Resolver;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Proxy;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class DoctrineProxyObjectResolver implements DoctrineProxyObjectResolverInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function resolve(PaymentRequestInterface $paymentRequest): void
    {
        // Resolve doctrine proxy object to 'real" one to be able to use it in Payum.
        $payment = $paymentRequest->getPayment();
        if ($payment instanceof Proxy) {
            $this->entityManager->detach($payment);
            $payment = $this->entityManager->find(PaymentInterface::class, $payment->getId());
            $paymentRequest->setPayment($payment);
        }
    }
}
