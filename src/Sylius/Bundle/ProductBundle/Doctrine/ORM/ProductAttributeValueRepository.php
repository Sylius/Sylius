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

namespace Sylius\Bundle\ProductBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Product\Repository\ProductAttributeValueRepositoryInterface;

class ProductAttributeValueRepository extends EntityRepository implements ProductAttributeValueRepositoryInterface
{
    public function findByJsonChoiceKey(string $choiceKey): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.json LIKE :key')
            ->setParameter('key', '%"' . $choiceKey . '"%')
            ->getQuery()
            ->getResult()
        ;
    }

    public function createByProductCodeAndLocaleQueryBuilder(
        string $productCode,
        string $localeCode,
        ?string $fallbackLocaleCode = null,
        ?string $defaultLocaleCode = null,
    ): QueryBuilder {
        $acceptableLocaleCodes = [$localeCode];

        if (null !== $fallbackLocaleCode) {
            $acceptableLocaleCodes[] = $fallbackLocaleCode;
        }

        if (null !== $defaultLocaleCode && array_count_values($acceptableLocaleCodes)[$localeCode] > 1) {
            $acceptableLocaleCodes[] = $defaultLocaleCode;
        }

        $subQuery = $this->createQueryBuilder('s')
            ->select('IDENTITY(s.attribute)')
            ->innerJoin('s.subject', 'subject')
            ->andWhere('subject.code = :code')
            ->andWhere('s.localeCode = :locale')
        ;

        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder
            ->innerJoin('o.subject', 'product')
            ->andWhere('product.code = :code')
            ->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->in('o.localeCode', $acceptableLocaleCodes),
                    $queryBuilder->expr()->isNull('o.localeCode'),
                ),
            )
            ->andWhere(
                $queryBuilder->expr()->orX(
                    'o.localeCode = :locale',
                    $queryBuilder->expr()->notIn('IDENTITY(o.attribute)', $subQuery->getDQL()),
                ),
            )
            ->setParameter('code', $productCode)
            ->setParameter('locale', $localeCode)
        ;
    }
}
