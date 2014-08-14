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

/**
 * Default product repository.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductRepository extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getQueryBuilder()
    {
        return parent::getQueryBuilder()
            ->select($this->getAlias().', option, variant')
            ->leftJoin($this->getAlias().'.options', 'option')
            ->leftJoin($this->getAlias().'.variants', 'variant')
        ;
    }

    /**
     * Return products for the given taxon ids
     *
     * @param array $ids
     *
     * @return array
     */
    public function getProductsByTaxons(array $ids)
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder
            ->innerJoin('product.taxons', 'taxon')
            ->andWhere('taxon.id IN ( :ids )')
            ->setParameter('ids', $ids)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    protected function getAlias()
    {
        return 'product';
    }
}
