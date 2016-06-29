<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ProductBundle\Doctrine\ORM;

use Sylius\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Product\Repository\VariantRepositoryInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class VariantRepository extends EntityRepository implements VariantRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneByName($name)
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation')
            ->where('translation.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilderWithProduct($productId)
    {
        return $this->createQueryBuilder('o')
            ->where('o.object = :productId')
            ->setParameter('productId', $productId)
        ;
    }
}
