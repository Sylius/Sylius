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

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Bridge\Doctrine\Types\UuidType;

/**
 * @template T of PaymentRequestInterface
 *
 * @implements PaymentRequestRepositoryInterface<T>
 *
 * @experimental
 */
class PaymentRequestRepository extends EntityRepository implements PaymentRequestRepositoryInterface
{
    public function findOneByPaymentId(mixed $hash, mixed $paymentId): ?PaymentRequestInterface
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder
            ->andWhere($queryBuilder->expr()->eq('o.hash', ':hash'))
            ->andWhere($queryBuilder->expr()->eq('o.payment', ':paymentId'))
            ->setParameter('hash', $hash, UuidType::NAME)
            ->setParameter('paymentId', $paymentId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function createQueryBuilderForPayment(string $paymentId): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq('o.payment', ':paymentId'))
            ->setParameter('paymentId', $paymentId)
        ;

        return $queryBuilder;
    }

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

    /**
     * @param array<string> $states
     *
     * @return array<PaymentRequestInterface>
     */
    public function findByPaymentIdAndStates(mixed $paymentId, array $states): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.payment = :paymentId')
            ->andWhere('o.state IN (:states)')
            ->setParameter('paymentId', $paymentId)
            ->setParameter('states', $states)
            ->getQuery()
            ->getResult()
        ;
    }
}
