<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Repository\ProductTaxonRepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ProductTaxonRepository extends EntityRepository implements ProductTaxonRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneByProductCodeAndTaxonCode($productCode, $taxonCode)
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.product', 'product')
            ->andWhere('product.code = :productCode')
            ->setParameter('productCode', $productCode)
            ->innerJoin('o.taxon', 'taxon')
            ->andWhere('taxon.code = :taxonCode')
            ->setParameter('taxonCode', $taxonCode)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
