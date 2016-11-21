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
     * @param CurrencyInterface $firstCurrency
     * @param CurrencyInterface $secondCurrency
     *
     * @return ExchangeRateInterface|null
     *
     * @throws NonUniqueResultException
     */
    public function findOneWithCurrencyPair(CurrencyInterface $firstCurrency, CurrencyInterface $secondCurrency)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('o.baseCurrency', $firstCurrency->getId()),
                    $queryBuilder->expr()->eq('o.counterCurrency', $secondCurrency->getId())
                ))
            ->orWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('o.baseCurrency', $secondCurrency->getId()),
                    $queryBuilder->expr()->eq('o.counterCurrency', $firstCurrency->getId())
                ))
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
