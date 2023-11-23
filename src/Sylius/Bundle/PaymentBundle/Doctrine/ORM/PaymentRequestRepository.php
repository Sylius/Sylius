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
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;

class PaymentRequestRepository extends EntityRepository implements PaymentRequestRepositoryInterface
{
    public function findExisting(PaymentInterface $payment, PaymentMethodInterface $paymentMethod, string $type): array
    {
            return $this->createQueryBuilder('o')
                ->innerJoin('o.payment', 'payment')
                ->innerJoin('o.method', 'method')
                ->andWhere('o.type = :type')
                ->andWhere('o.payment = :payment')
                ->andWhere('o.method = :method')
                ->setParameter('type', $type)
                ->setParameter('method', $paymentMethod)
                ->setParameter('payment', $payment)
                ->getQuery()
                ->getResult()
            ;
    }
}
