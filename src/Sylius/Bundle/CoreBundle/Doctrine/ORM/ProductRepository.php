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

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\PagerfantaInterface;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;

/**
 * Product repository.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ProductRepository extends BaseProductRepository
{
    /**
     * Create paginator for products categorized under given taxon.
     *
     * @param TaxonInterface $taxon
     * @param array          $criteria
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function createByTaxonPaginator(TaxonInterface $taxon, array $criteria = array())
    {
        $queryBuilder = $this->objectRepository->createQueryBuilder('o');

        $queryBuilder
            ->innerJoin('product.taxons', 'taxon')
            ->andWhere($queryBuilder->expr()->orX(
                'taxon = :taxon',
                ':left < taxon.left AND taxon.right < :right'
            ))
            ->setParameter('taxon', $taxon)
            ->setParameter('left', $taxon->getLeft())
            ->setParameter('right', $taxon->getRight())
        ;

        $this->applyCriteria($queryBuilder, $criteria);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * Create paginator for products categorized under given taxon.
     *
     * @param TaxonInterface $taxon
     *
     * @return PagerfantaInterface
     */
    public function createByTaxonAndChannelPaginator(TaxonInterface $taxon, ChannelInterface $channel)
    {
        $queryBuilder = $this->objectRepository->createQueryBuilder('o');

        $queryBuilder
            ->innerJoin('o.taxons', 'taxon')
            ->innerJoin('o.channels', 'channel')
            ->andWhere('taxon = :taxon')
            ->andWhere('channel = :channel')
            ->setParameter('channel', $channel)
            ->setParameter('taxon', $taxon)
        ;

        return $this->getPaginator($queryBuilder);
    }

    /**
     * Create filter paginator.
     *
     * @param array $criteria
     * @param array $sorting
     * @param bool  $deleted
     *
     * @return PagerfantaInterface
     */
    public function createFilterPaginator($criteria = array(), $sorting = array(), $deleted = false)
    {
        $queryBuilder = $this->objectRepository->createQueryBuilder('o')
            ->addSelect('variant')
            ->addSelect('translation')
            ->leftJoin('o.variants', 'variant')
            ->leftJoin('o.translations', 'translation')
        ;

        if (!empty($criteria['name'])) {
            $queryBuilder
                ->andWhere('translation.name LIKE :name')
                ->setParameter('name', '%'.$criteria['name'].'%')
            ;
        }
        if (!empty($criteria['sku'])) {
            $queryBuilder
                ->andWhere('variant.sku = :sku')
                ->setParameter('sku', $criteria['sku'])
            ;
        }

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = array();
            }

            $sorting['updatedAt'] = 'desc';
        }

        $this->applySorting($queryBuilder, $sorting);

        if ($deleted) {
            $this->objectManager->getFilters()->disable('softdeleteable');
            $queryBuilder->andWhere('o.deletedAt IS NOT NULL');
        }

        return $this->getPaginator($queryBuilder);
    }

    /**
     * Get the product data for the details page.
     *
     * @param int $id
     *
     * @return null|ProductInterface
     */
    public function findForDetailsPage($id)
    {
        $this->objectManager->getFilters()->disable('softdeleteable');

        $queryBuilder = $this->objectRepository->createQueryBuilder('o');

        $queryBuilder
            ->leftJoin('o.variants', 'variant')
            ->addSelect('variant')
            ->leftJoin('variant.images', 'image')
            ->addSelect('image')
            ->andWhere($queryBuilder->expr()->eq('o.id', ':id'))
            ->setParameter('id', $id)
        ;

        $result = $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->objectManager->getFilters()->enable('softdeleteable');

        return $result;
    }

    /**
     * Find X recently added products.
     *
     * @param int              $limit
     * @param ChannelInterface $channel
     *
     * @return ProductInterface[]
     */
    public function findLatest($limit = 10, ChannelInterface $channel)
    {
        return $this->findBy(array('channels' => array($channel)), array('createdAt' => 'desc'), $limit);
    }

    protected function applyCriteria(QueryBuilder $queryBuilder, array $criteria = null)
    {
        if (isset($criteria['channels'])) {
            $queryBuilder
                ->innerJoin('o.channels', 'channel')
                ->andWhere('channel = :channel')
                ->setParameter('channel', $criteria['channels'])
            ;

            unset($criteria['channels']);
        }

        parent::applyCriteria($queryBuilder, $criteria);
    }
}
