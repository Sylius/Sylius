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
use Sylius\Component\Core\Model\ProductInterface;
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
    public function createListQueryBuilder($locale, $taxonId = null)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation')
            ->andWhere('translation.locale = :locale')
            ->setParameter('locale', $locale)
        ;

        if (null !== $taxonId) {
            $queryBuilder
                ->innerJoin('o.productTaxons', 'productTaxon')
                ->andWhere('productTaxon.taxon = :taxonId')
                ->setParameter('taxonId', $taxonId)
            ;
        }

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilderByChannelAndTaxonSlug(ChannelInterface $channel, $taxonSlug, $locale)
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.productTaxons', 'productTaxon')
            ->innerJoin('o.channels', 'channel')
            ->leftJoin('o.translations', 'translation')
            ->leftJoin('productTaxon.taxon', 'taxon')
            ->leftJoin('taxon.translations', 'taxonTranslation')
            ->andWhere('translation.locale = :locale')
            ->andWhere('taxonTranslation.locale = :locale')
            ->andWhere('taxonTranslation.slug = :taxonSlug')
            ->andWhere('channel = :channel')
            ->andWhere('o.enabled = true')
            ->setParameter('locale', $locale)
            ->setParameter('taxonSlug', $taxonSlug)
            ->setParameter('channel', $channel)
        ;
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
    public function findOneBySlugAndChannel($slug, ChannelInterface $channel)
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.translations', 'translation')
            ->innerJoin('o.channels', 'channel')
            ->andWhere('translation.slug = :slug')
            ->andWhere('channel = :channel')
            ->andWhere('o.enabled = true')
            ->setParameter('slug', $slug)
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySlug($slug)
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.translations', 'translation')
            ->andWhere('translation.slug = :slug')
            ->andWhere('o.enabled = true')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
