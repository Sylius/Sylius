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

namespace Sylius\Component\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface as BaseProductVariantRepositoryInterface;

/**
 * @template T of ProductVariantInterface
 *
 * @extends BaseProductVariantRepositoryInterface<T>
 */
interface ProductVariantRepositoryInterface extends BaseProductVariantRepositoryInterface
{
    public function createInventoryListQueryBuilder(string $locale): QueryBuilder;

    public function findByTaxon(TaxonInterface $taxon): array;

    public function createCatalogPromotionListQueryBuilder(
        string $locale,
        CatalogPromotionInterface $catalogPromotion,
    ): QueryBuilder;
}
