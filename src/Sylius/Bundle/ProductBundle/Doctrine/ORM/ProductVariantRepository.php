<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class ProductVariantRepository extends EntityRepository implements ProductVariantRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createQueryBuilderByProductId($locale, $productId)
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
    public function createQueryBuilderByProductCode($locale, $productCode)
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
    public function findByName($name, $locale)
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
    public function findByNameAndProduct($name, $locale, ProductInterface $product)
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
    public function findOneByCodeAndProductCode($code, $productCode)
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
    public function findByCodeAndProductCode($code, $productCode)
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.product', 'product')
            ->andWhere('product.code = :productCode')
            ->andWhere('o.code = :code')
            ->setParameter('productCode', $productCode)
            ->setParameter('code', $code)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByIdAndProductId($id, $productId)
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
    public function findByPhraseAndProductCode($phrase, $locale, $productCode)
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
            ->setParameter('phrase', '%'.$phrase.'%')
            ->setParameter('locale', $locale)
            ->setParameter('productCode', $productCode)
            ->getQuery()
            ->getResult()
        ;
    }
}
