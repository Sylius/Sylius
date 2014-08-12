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
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;
use Doctrine\ORM\Query;

/**
 * @author agounaris <agounaris@gmail.com>
 */
class SyliusSearchIndexRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductRepository
     */
    private $productRepository;

    /**
     * @param EntityManager         $em
     * @param BaseProductRepository $productRepository
     */
    public function __construct(EntityManager $em, BaseProductRepository $productRepository)
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
        $query = $this->em->createQuery('SELECT u.id FROM Sylius\Component\Core\Model\Taxon u WHERE u.name = ?1');
        $query->setParameter(1, $taxonName);
        $taxonId = $query->getResult(Query::HYDRATE_SINGLE_SCALAR);

        $filteredProducts = $this->productRepository->getProductsByTaxons(array($taxonId));

        $filteredIds = array();
        foreach ($filteredProducts as $filteredProduct) {
            $filteredIds[] = $filteredProduct->getId();
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