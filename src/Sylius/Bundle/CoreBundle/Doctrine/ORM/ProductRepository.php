<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use SyliusLabs\AssociationHydrator\AssociationHydrator;

/**
 * @template T of ProductInterface
 *
 * @extends BaseProductRepository<T>
 *
 * @implements ProductRepositoryInterface<T>
 */
class ProductRepository extends BaseProductRepository implements ProductRepositoryInterface
{
    protected AssociationHydrator $associationHydrator;

    public function __construct(EntityManager $entityManager, ClassMetadata $class)
    {
        parent::__construct($entityManager, $class);

        $this->associationHydrator = new AssociationHydrator($entityManager, $class);
    }

    public function createListQueryBuilder(string $locale, $taxonId = null): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
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

    public function createShopListQueryBuilder(
        ChannelInterface $channel,
        TaxonInterface $taxon,
        string $locale,
        array $sorting = [],
        bool $includeAllDescendants = false,
    ): QueryBuilder {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->addSelect('productTaxon')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->innerJoin('o.productTaxons', 'productTaxon')
        ;

        if ($includeAllDescendants) {
            $queryBuilder
                ->innerJoin('productTaxon.taxon', 'taxon')
                ->andWhere('taxon.left >= :taxonLeft')
                ->andWhere('taxon.right <= :taxonRight')
                ->andWhere('taxon.root = :taxonRoot')
                ->setParameter('taxonLeft', $taxon->getLeft())
                ->setParameter('taxonRight', $taxon->getRight())
                ->setParameter('taxonRoot', $taxon->getRoot())
            ;
        } else {
            $queryBuilder
                ->andWhere('productTaxon.taxon = :taxon')
                ->setParameter('taxon', $taxon)
            ;
        }

        if (empty($sorting)) {
            $queryBuilder
                ->leftJoin('o.productTaxons', 'productTaxons', 'WITH', 'productTaxons.taxon = :taxonId')
                ->orderBy('productTaxons.position', 'ASC')
                ->setParameter('taxonId', $taxon->getId())
            ;
        }

        $queryBuilder
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = :enabled')
            ->setParameter('locale', $locale)
            ->setParameter('channel', $channel)
            ->setParameter('enabled', true)
        ;

        // Grid hack, we do not need to join these if we don't sort by price
        if (isset($sorting['price'])) {
            // Another hack, the subquery to get the first position variant
            $subQuery = $this->createQueryBuilder('m')
                 ->select('min(v.position)')
                 ->innerJoin('m.variants', 'v')
                 ->andWhere('m.id = :product_id')
                 ->andWhere('v.enabled = :enabled')
            ;

            $queryBuilder
                ->addSelect('variant')
                ->addSelect('channelPricing')
                ->innerJoin('o.variants', 'variant')
                ->innerJoin('variant.channelPricings', 'channelPricing')
                ->andWhere('channelPricing.channelCode = :channelCode')
                ->andWhere(
                    $queryBuilder->expr()->in(
                        'variant.position',
                        str_replace(':product_id', 'o.id', $subQuery->getDQL()),
                    ),
                )
                ->setParameter('channelCode', $channel->getCode())
                ->setParameter('enabled', true)
            ;
        }

        return $queryBuilder;
    }

    public function findLatestByChannel(ChannelInterface $channel, string $locale, int $count): array
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = :enabled')
            ->addOrderBy('o.createdAt', 'DESC')
            ->setParameter('channel', $channel)
            ->setParameter('locale', $locale)
            ->setParameter('enabled', true)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByChannelAndSlug(ChannelInterface $channel, string $locale, string $slug): ?ProductInterface
    {
        $product = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->andWhere('translation.slug = :slug')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = :enabled')
            ->setParameter('channel', $channel)
            ->setParameter('locale', $locale)
            ->setParameter('slug', $slug)
            ->setParameter('enabled', true)
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

    public function findOneByChannelAndCode(ChannelInterface $channel, string $code): ?ProductInterface
    {
        $product = $this->createQueryBuilder('o')
            ->where('o.code = :code')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = :enabled')
            ->setParameter('channel', $channel)
            ->setParameter('code', $code)
            ->setParameter('enabled', true)
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

    public function findOneByCode(string $code): ?ProductInterface
    {
        return $this->createQueryBuilder('o')
            ->where('o.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByIdHydrated(mixed $id): ?ProductInterface
    {
        $product = $this->createQueryBuilder('o')
            ->where('o.id = :id')
            ->setParameter('id', $id)
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
            'attributes',
            'attributes.attribute',
            'attributes.attribute.translations',
        ]);

        return $product;
    }

    public function findByTaxon(TaxonInterface $taxon): array
    {
        return $this
            ->createQueryBuilder('product')
            ->distinct()
            ->addSelect('productTaxon')
            ->innerJoin('product.productTaxons', 'productTaxon')
            ->andWhere('productTaxon.taxon = :taxon')
            ->setParameter('taxon', $taxon)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByChannelAndCodeWithAvailableAssociations(ChannelInterface $channel, string $code): ?ProductInterface
    {
        $product = $this->createQueryBuilder('o')
            ->addSelect('association')
            ->leftJoin('o.associations', 'association')
            ->innerJoin('association.associatedProducts', 'associatedProduct', 'WITH', 'associatedProduct.enabled = :enabled')
            ->where('o.code = :code')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = :enabled')
            ->setParameter('channel', $channel)
            ->setParameter('code', $code)
            ->setParameter('enabled', true)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (null === $product) {
            $product = $this->findOneByChannelAndCode($channel, $code);
            if (null === $product) {
                return null;
            }

            $product->getAssociations()->clear();
        }

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
}
