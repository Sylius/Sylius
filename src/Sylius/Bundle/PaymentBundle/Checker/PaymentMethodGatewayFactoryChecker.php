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

namespace Sylius\Bundle\PaymentBundle\Checker;

use Doctrine\ORM\EntityRepository;
use Sylius\Component\Payment\Checker\PaymentMethodGatewayFactoryCheckerInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;

final class PaymentMethodGatewayFactoryChecker implements PaymentMethodGatewayFactoryCheckerInterface
{
    /** @param PaymentMethodRepositoryInterface<PaymentMethodInterface>&EntityRepository $paymentMethodRepository */
    public function __construct(
        private readonly PaymentMethodRepositoryInterface $paymentMethodRepository,
    ) {
    }

    public function hasPaymentMethodWithGatewayFactory(string $factoryName): bool
    {
        $count = (int) $this->paymentMethodRepository->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->innerJoin('o.gatewayConfig', 'gatewayConfig')
            ->andWhere('gatewayConfig.factoryName = :factoryName')
            ->setParameter('factoryName', $factoryName)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count > 0;
    }
}
