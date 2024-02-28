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

namespace Sylius\Bundle\PaymentBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;

/**
 * @template T of PaymentRequestInterface
 *
 * @implements PaymentRequestRepositoryInterface<T>
 */
class PaymentRequestRepository extends EntityRepository implements PaymentRequestRepositoryInterface
{
    public function duplicateExists(PaymentRequestInterface $paymentRequest): bool
    {
        /** @var PaymentRequestInterface[] $paymentRequests */
        $paymentRequests = $this->createQueryBuilder('o')
            ->innerJoin('o.payment', 'payment')
            ->innerJoin('o.method', 'method')
            ->where('o != :paymentRequest')
            ->andWhere('o.action = :action')
            ->andWhere('o.payment = :payment')
            ->andWhere('o.method = :method')
            // Symfony\Bridge\Doctrine\Types\UuidType has signature changes between sf 5.4 and 6.4
            // We are forced to use type: "uuid" to use both Sf versions
            ->setParameter('paymentRequest', $paymentRequest->getHash(), 'uuid')
            ->setParameter('action', $paymentRequest->getAction())
            ->setParameter('method', $paymentRequest->getMethod())
            ->setParameter('payment', $paymentRequest->getPayment())
            ->getQuery()
            ->getResult()
        ;

        return count($paymentRequests) > 0;
    }

    public function findOneByHash(string $hash): ?PaymentRequestInterface
    {
        return $this->createQueryBuilder('o')
            ->addSelect('payment')
            ->addSelect('method')
            ->innerJoin('o.payment', 'payment')
            ->innerJoin('o.method', 'method')
            ->where('o.hash = :hash')
            ->setParameter('hash', $hash)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
