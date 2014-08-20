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
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @author agounaris <agounaris@gmail.com>
 */
class SearchIndexRepository extends EntityRepository
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
            ->select('product')
            ->from('Sylius\Component\Core\Model\Product', 'product')
            ->leftJoin('product.taxons', 'taxon')
            ->where('taxon.name = :taxonName')
            ->setParameter('taxonName', $taxonName)
        ;

        $filteredIds = array();
        $products = $queryBuilder->getQuery()->getResult();

        foreach ($products as $product) {
            $filteredIds[get_class($product)][] = $product->getId();
        }

        return $filteredIds;
    }

    /**
     * @param $resultSet
     * @return Pagerfanta
     */
    public function hydrateSearchResults($resultSetFromFulltextSearch = array())
    {
        $results = array();
        foreach ($resultSetFromFulltextSearch as $model=>$ids) {

            $queryBuilder = $this->em->createQueryBuilder();
            $queryBuilder
                ->select('u')
                ->from($model, 'u')
                ->where('u.id IN (:ids)')
                ->setParameter('ids', $ids)
                ;

            $objects = $queryBuilder->getQuery()->getResult();

            foreach ($objects as $object) {
                $results[] = $object;
            }
        }

        return $results;
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