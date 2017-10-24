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

namespace Sylius\Component\Product\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ProductVariantRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $locale
     * @param mixed $productId
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderByProductId(string $locale, $productId): QueryBuilder;

    /**
     * @param string $locale
     * @param string $productCode
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderByProductCode(string $locale, string $productCode): QueryBuilder;

    /**
     * @param string $name
     * @param string $locale
     *
     * @return array|ProductVariantInterface[]
     */
    public function findByName(string $name, string $locale): array;

    /**
     * @param string $name
     * @param string $locale
     * @param ProductInterface $product
     *
     * @return array|ProductVariantInterface[]
     */
    public function findByNameAndProduct(string $name, string $locale, ProductInterface $product): array;

    /**
     * @param string $code
     * @param string $productCode
     *
     * @return ProductVariantInterface|null
     */
    public function findOneByCodeAndProductCode(string $code, string $productCode): ?ProductVariantInterface;

    /**
     * @param array|string[] $codes
     * @param string $productCode
     *
     * @return array|ProductVariantInterface[]
     */
    public function findByCodesAndProductCode(array $codes, string $productCode): array;

    /**
     * @param mixed $id
     * @param mixed $productId
     *
     * @return ProductVariantInterface|null
     */
    public function findOneByIdAndProductId($id, $productId): ?ProductVariantInterface;

    /**
     * @param string $phrase
     * @param string $locale
     * @param string $productCode
     *
     * @return array|ProductVariantInterface[]
     */
    public function findByPhraseAndProductCode(string $phrase, string $locale, string $productCode): array;
}
