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
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Product\Repository\ProductRepositoryInterface;

/**
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class SearchIndexRepository extends EntityRepository
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param EntityManager $entityManager
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(EntityManager $entityManager, ProductRepositoryInterface $productRepository)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
    }

    /**
     * @param string $taxonName
     *
     * @return array
     */
    public function getProductIdsFromTaxonName($taxonName)
    {
        $productClassName = $this->productRepository->getClassName();

        // Gets the taxon ids
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('product')
            ->from($productClassName, 'product')
            ->leftJoin('product.taxons', 'taxon')
            ->where('taxon.name = :taxonName')
            ->setParameter('taxonName', $taxonName)
        ;

        $filteredIds = [];
        foreach ($queryBuilder->getQuery()->getArrayResult() as $product) {
            $filteredIds[$productClassName][] = $product['id'];
        }

        return $filteredIds;
    }

    public function getProductIdsFromChannel(ChannelInterface $channel)
    {
        $productClassName = $this->productRepository->getClassName();

        // Gets the taxon ids
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('product.id')
            ->from($productClassName, 'product')
            ->leftJoin('product.channels', 'channel')
            ->where('channel.id = :channel')
            ->setParameter('channel', $channel->getId())
        ;

        $filteredIds = [];
        foreach ($queryBuilder->getQuery()->getArrayResult() as $product) {
            $filteredIds[$productClassName][] = $product['id'];
        }

        return $filteredIds;
    }

    /**
     * @param array $resultSetFromFulltextSearch
     *
     * @return array
     */
    public function hydrateSearchResults($resultSetFromFulltextSearch = [])
    {
        $results = [];
        foreach ($resultSetFromFulltextSearch as $model => $ids) {
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder
                ->select('u')
                ->from($model, 'u')
                ->where('u.id IN (:ids)')
                ->setParameter('ids', $ids)
            ;

            foreach ($queryBuilder->getQuery()->getResult() as $object) {
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
        return $this->productRepository->findBy(['id' => $ids]);
    }

    public function getArrayPaginator($objects)
    {
        return parent::getArrayPaginator($objects);
    }
}
