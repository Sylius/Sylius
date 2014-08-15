<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Doctrine\ORM;

use Doctrine\ORM\EntityManager;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductRepository;
use Doctrine\ORM\Query;

/**
 * @author agounaris <agounaris@gmail.com>
 */
class SyliusSearchIndexRepository
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param EntityManager         $em
     * @param ProductRepository $productRepository
     */
    public function __construct(EntityManager $em, ProductRepository $productRepository)
    {
        $this->em = $em;
        $this->productRepository = $productRepository;
    }

    /**
     * Returns the product ids for a given taxon
     *
     * @param $taxonName
     *
     * @return array
     */
    public function getProductIdsFromTaxonName($taxonName)
    {
        // Gets the taxon ids
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('product.id')
            ->from('Sylius\Component\Core\Model\Product', 'product')
            ->leftJoin('product.taxons', 'taxon')
            ->where('taxon.name = :taxonName')
            ->setParameter('taxonName', $taxonName)
        ;

        $filteredIds = array();
        foreach ($queryBuilder->getQuery()->getResult(Query::HYDRATE_ARRAY) as $row) {
            $filteredIds[] = $row['id'];
        }

        return $filteredIds;
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return mixed|\Pagerfanta\Pagerfanta
     */
    public function createPaginator(array $criteria = null, array $orderBy = null)
    {
        return $this->productRepository->createPaginator($criteria);
    }

    /**
     * @param array $ids
     *
     * @return array
     */
    public function getProductsByIds(array $ids)
    {
        return $this->productRepository->findBy(array('id'=>$ids));
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getProductsQueryBuilder()
    {
        return $this->productRepository->getCollectionQueryBuilder();
    }

} 