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
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ProductRepository extends BaseProductRepository implements ProductRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createListQueryBuilder()
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createByTaxonPaginator(TaxonInterface $taxon, array $criteria = [])
    {
        $root = $taxon->isRoot() ? $taxon : $taxon->getRoot();

        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder
            ->innerJoin('o.taxons', 'taxon')
            ->andWhere($queryBuilder->expr()->eq('taxon.root', ':root'))
            ->andWhere($queryBuilder->expr()->orX(
                'taxon = :taxon',
                ':left < taxon.left AND taxon.right < :right'
            ))
            ->setParameter('root', $root)
            ->setParameter('taxon', $taxon)
            ->setParameter('left', $taxon->getLeft())
            ->setParameter('right', $taxon->getRight())
        ;

        $this->applyCriteria($queryBuilder, $criteria);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function createByTaxonAndChannelPaginator(TaxonInterface $taxon, ChannelInterface $channel)
    {
        $queryBuilder = $this->createQueryBuilder('o')
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
     * {@inheritdoc}
     */
    public function createFilterPaginator(array $criteria = null, array $sorting = null)
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation')
            ->addSelect('variant')
            ->leftJoin('o.variants', 'variant')
        ;

        if (!empty($criteria['name'])) {
            $queryBuilder
                ->andWhere('translation.name LIKE :name')
                ->setParameter('name', '%'.$criteria['name'].'%')
            ;
        }
        if (!empty($criteria['code'])) {
            $queryBuilder
                ->andWhere('variant.code = :code')
                ->setParameter('code', $criteria['code'])
            ;
        }

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = [];
            }
            $sorting['updatedAt'] = 'desc';
        }

        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function findForDetailsPage($id)
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder
            ->select('o, option, variant')
            ->leftJoin('o.options', 'option')
            ->leftJoin('o.variants', 'variant')
            ->leftJoin('variant.images', 'image')
            ->addSelect('image')
            ->andWhere($queryBuilder->expr()->eq('o.id', ':id'))
            ->setParameter('id', $id)
        ;

        $result = $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findLatestByChannel(ChannelInterface $channel, $count)
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.channels', 'channel')
            ->addOrderBy('o.createdAt', 'desc')
            ->andWhere('o.enabled = true')
            ->andWhere('channel = :channel')
            ->setParameter('channel', $channel)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByIdAndChannel($id, ChannelInterface $channel = null)
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('image')
            ->select('o, option, variant')
            ->leftJoin('o.options', 'option')
            ->leftJoin('o.variants', 'variant')
            ->leftJoin('variant.images', 'image')
            ->innerJoin('o.channels', 'channel')
        ;

        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq('o.id', ':id'))
            ->setParameter('id', $id)
        ;

        if (null !== $channel) {
            $queryBuilder
                ->andWhere('channel = :channel')
                ->setParameter('channel', $channel);
        }

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findEnabledByTaxonCodeAndChannel($code, ChannelInterface $channel)
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.taxons', 'taxon')
            ->andWhere('taxon.code = :code')
            ->innerJoin('o.channels', 'channel')
            ->andWhere('channel = :channel')
            ->andWhere('o.enabled = true')
            ->setParameter('code', $code)
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySlugAndChannel($slug, ChannelInterface $channel)
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.translations', 'translation')
            ->innerJoin('o.channels', 'channel')
            ->andWhere('channel = :channel')
            ->andWhere('o.enabled = true')
            ->andWhere('translation.slug = :slug')
            ->setParameter('slug', $slug)
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
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
