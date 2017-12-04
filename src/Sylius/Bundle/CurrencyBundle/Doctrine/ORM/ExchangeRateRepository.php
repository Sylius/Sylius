<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CurrencyBundle\Doctrine\ORM;

use Doctrine\ORM\NonUniqueResultException;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Component\Currency\Repository\ExchangeRateRepositoryInterface;

class ExchangeRateRepository extends EntityRepository implements ExchangeRateRepositoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws NonUniqueResultException
     */
    public function findOneWithCurrencyPair(string $firstCurrencyCode, string $secondCurrencyCode): ?ExchangeRateInterface
    {
        $expr = $this->getEntityManager()->getExpressionBuilder();

        return $this->createQueryBuilder('o')
            ->addSelect('sourceCurrency')
            ->addSelect('targetCurrency')
            ->innerJoin('o.sourceCurrency', 'sourceCurrency')
            ->innerJoin('o.targetCurrency', 'targetCurrency')
            ->andWhere($expr->orX(
                'sourceCurrency.code = :firstCurrency AND targetCurrency.code = :secondCurrency',
                'targetCurrency.code = :firstCurrency AND sourceCurrency.code = :secondCurrency'
            ))
            ->setParameter('firstCurrency', $firstCurrencyCode)
            ->setParameter('secondCurrency', $secondCurrencyCode)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
