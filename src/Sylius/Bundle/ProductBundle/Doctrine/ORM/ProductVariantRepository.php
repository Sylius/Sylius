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
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;

class ProductVariantRepository extends EntityRepository implements ProductVariantRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createQueryBuilderByProductId(string $locale, $productId): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('translation.locale = :locale')
            ->andWhere('o.product = :productId')
            ->setParameter('locale', $locale)
            ->setParameter('productId', $productId)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilderByProductCode(string $locale, string $productCode): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation')
            ->innerJoin('o.product', 'product')
            ->andWhere('translation.locale = :locale')
            ->andWhere('product.code = :productCode')
            ->setParameter('locale', $locale)
            ->setParameter('productCode', $productCode)
        ;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function findByNameAndProduct(string $name, string $locale, ProductInterface $product): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('translation.name = :name')
            ->andWhere('translation.locale = :locale')
            ->andWhere('o.product = :product')
            ->setParameter('name', $name)
            ->setParameter('locale', $locale)
            ->setParameter('product', $product)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByCodeAndProductCode(string $code, string $productCode): ?ProductVariantInterface
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.product', 'product')
            ->andWhere('product.code = :productCode')
            ->andWhere('o.code = :code')
            ->setParameter('productCode', $productCode)
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findByCodesAndProductCode(array $codes, string $productCode): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.product', 'product')
            ->andWhere('product.code = :productCode')
            ->andWhere('o.code IN (:codes)')
            ->setParameter('productCode', $productCode)
            ->setParameter('codes', $codes)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByIdAndProductId($id, $productId): ?ProductVariantInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.product = :productId')
            ->andWhere('o.id = :id')
            ->setParameter('productId', $productId)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findByPhraseAndProductCode(string $phrase, string $locale, string $productCode): array
    {
        $expr = $this->getEntityManager()->getExpressionBuilder();

        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->innerJoin('o.product', 'product')
            ->andWhere('product.code = :productCode')
            ->andWhere($expr->orX(
                'translation.name LIKE :phrase',
                'o.code LIKE :phrase'
            ))
            ->setParameter('phrase', '%' . $phrase . '%')
            ->setParameter('locale', $locale)
            ->setParameter('productCode', $productCode)
            ->getQuery()
            ->getResult()
        ;
    }
}
