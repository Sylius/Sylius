<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductVariantRepository as BaseProductVariantRepository;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

class ProductVariantRepository extends BaseProductVariantRepository implements ProductVariantRepositoryInterface
{
    public function createInventoryListQueryBuilder(string $locale): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->andWhere('o.tracked = :tracked')
            ->setParameter('locale', $locale)
            ->setParameter('tracked', true)
        ;
    }

    public function findByTaxon(TaxonInterface $taxon): array
    {
        return $this
            ->createQueryBuilder('variant')
            ->innerJoin('variant.product', 'product')
            ->innerJoin('product.productTaxons', 'productTaxon')
            ->andWhere('productTaxon.taxon = :taxon')
            ->setParameter('taxon', $taxon)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByCatalogPromotion(): QueryBuilder
    {
        return $this->createQueryBuilder('jan_pawel_2')
            ->andWhere('jan_pawel_2.id < 5000');
    }
}
