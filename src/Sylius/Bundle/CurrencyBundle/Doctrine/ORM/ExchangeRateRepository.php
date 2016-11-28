<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CurrencyBundle\Doctrine\ORM;

use Doctrine\ORM\NonUniqueResultException;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Component\Currency\Repository\ExchangeRateRepositoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ExchangeRateRepository extends EntityRepository implements ExchangeRateRepositoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws NonUniqueResultException
     */
    public function findOneWithCurrencyPair($firstCurrencyCode, $secondCurrencyCode)
    {
        $queryBuilder = $this
            ->createQueryBuilder('o')
            ->addSelect('sourceCurrency')
            ->leftJoin('o.sourceCurrency', 'sourceCurrency')
            ->addSelect('targetCurrency')
            ->leftJoin('o.targetCurrency', 'targetCurrency')
        ;

        return $queryBuilder
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('sourceCurrency.code', ':firstCurrency'),
                    $queryBuilder->expr()->eq('targetCurrency.code', ':secondCurrency')
                ))
            ->orWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('sourceCurrency.code', ':secondCurrency'),
                    $queryBuilder->expr()->eq('targetCurrency.code', ':firstCurrency')
                ))
            ->setParameter('firstCurrency', $firstCurrencyCode)
            ->setParameter('secondCurrency', $secondCurrencyCode)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
