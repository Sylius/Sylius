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

namespace Sylius\Bundle\ProductBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Repository\ProductOptionRepositoryInterface;

/**
 * @template T of ProductOptionInterface
 *
 * @implements ProductOptionRepositoryInterface<T>
 */
class ProductOptionRepository extends EntityRepository implements ProductOptionRepositoryInterface
{
    public function createListQueryBuilder(string $locale): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('translation.locale = :locale')
            ->setParameter('locale', $locale)
        ;
    }

    public function findByName(string $name, string $locale): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('translation.name = :name')
            ->andWhere('translation.locale = :locale')
            ->setParameter('name', $name)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByPhrase(string $phrase, string $locale, int $limit = 10): array
    {
        $subqueryBuilder = $this->createQueryBuilder('sq')
            ->innerJoin('sq.translations', 'translation', 'WITH', 'translation.name LIKE :name')
            ->groupBy('sq.id')
            ->addGroupBy('translation.translatable')
            ->orderBy('translation.translatable', 'DESC')
        ;

        $queryBuilder = $this->createQueryBuilder('o');

        /** @var ProductOptionInterface[] $results */
        $results = $queryBuilder
            ->andWhere($queryBuilder->expr()->in('o', $subqueryBuilder->getDQL()))
            ->setParameter('name', '%' . $phrase . '%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;

        foreach ($results as $result) {
            $result->setFallbackLocale(array_key_first($result->getTranslations()->toArray()));
        }

        return $results;
    }

    public function findByCodes(array $codes = []): array
    {
        $expr = $this->getEntityManager()->getExpressionBuilder();

        return $this->createQueryBuilder('o')
            ->andWhere($expr->in('o.code', ':codes'))
            ->setParameter('codes', $codes)
            ->getQuery()
            ->getResult()
        ;
    }
}
