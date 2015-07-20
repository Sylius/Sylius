<?php

namespace Sylius\BackendBundle\Repository;

use \Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;



/**
 * Class ProductRepository.
 */
class ProductRepository extends BaseProductRepository
{



    public function countProducts($excluded = false, $availableOn = null)
    {

        $this->_em->getFilters()->enable('softdeleteable');

        if (!$availableOn) {
            $availableOn = new \DateTime('midnight');
        }

//        if ($excluded === true) {
//            $this->_em->getFilters()->enable('softdeleteable');
//        } else {
//            $this->_em->getFilters()->disable('softdeleteable');
//        }

        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder
            ->select('count(product.id)')
            ->where('product.availableOn <= :available')
            ->setParameter('available', $availableOn);

        return (int) $queryBuilder
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }



    /**
     * {@inheritdoc}
     */
    protected function getQueryBuilder()
    {
        return parent::getQueryBuilder();
    }
}
