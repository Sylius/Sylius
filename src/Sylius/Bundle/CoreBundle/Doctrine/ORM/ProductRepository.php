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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping;
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
     * @var AssociationHydrator
     */
    private $associationHydrator;

    /**
     * @param EntityManager $em
     * @param Mapping\ClassMetadata $class
     */
    public function __construct(EntityManager $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);

        $this->associationHydrator = new AssociationHydrator($em, $class);
    }

    /**
     * {@inheritdoc}
     */
    public function createListQueryBuilder($locale, $taxonId = null)
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
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
    public function createShopListQueryBuilder(ChannelInterface $channel, TaxonInterface $taxon, $locale, array $sorting = [])
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->innerJoin('o.productTaxons', 'productTaxon')
            ->andWhere('productTaxon.taxon = :taxon')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->addGroupBy('o.id')
            ->setParameter('locale', $locale)
            ->setParameter('taxon', $taxon)
            ->setParameter('channel', $channel)
        ;

        // Grid hack, we do not need to join these if we don't sort by price
        if (isset($sorting['price'])) {
            $queryBuilder
                ->innerJoin('o.variants', 'variant')
                ->innerJoin('variant.channelPricings', 'channelPricing')
                ->andWhere('channelPricing.channelCode = :channelCode')
                ->setParameter('channelCode', $channel->getCode())
            ;
        }

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function findLatestByChannel(ChannelInterface $channel, $locale, $count)
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->addOrderBy('o.createdAt', 'DESC')
            ->setParameter('channel', $channel)
            ->setParameter('locale', $locale)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByChannelAndSlug(ChannelInterface $channel, $locale, $slug)
    {
        $product = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->andWhere('translation.slug = :slug')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->setParameter('channel', $channel)
            ->setParameter('locale', $locale)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->associationHydrator->hydrateAssociations($product, [
            'images',
            'options',
            'options.translations',
            'variants',
            'variants.channelPricings',
            'variants.optionValues',
            'variants.optionValues.translations',
        ]);

        return $product;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByCode($code)
    {
        return $this->createQueryBuilder('o')
            ->where('o.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
