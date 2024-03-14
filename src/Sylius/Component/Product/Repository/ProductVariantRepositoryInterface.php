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

namespace Sylius\Component\Product\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of ProductVariantInterface
 *
 * @extends RepositoryInterface<T>
 */
interface ProductVariantRepositoryInterface extends RepositoryInterface
{
    public function createQueryBuilderByProductId(string $locale, $productId): QueryBuilder;

    public function createQueryBuilderByProductCode(string $locale, string $productCode): QueryBuilder;

    /**
     * @return array|ProductVariantInterface[]
     */
    public function findByName(string $name, string $locale): array;

    /**
     * @return array|ProductVariantInterface[]
     */
    public function findByNameAndProduct(string $name, string $locale, ProductInterface $product): array;

    public function findOneByCodeAndProductCode(string $code, string $productCode): ?ProductVariantInterface;

    /**
     * @param array|string[] $codes
     *
     * @return array|ProductVariantInterface[]
     */
    public function findByCodesAndProductCode(array $codes, string $productCode): array;

    /**
     * @param array|string[] $codes
     *
     * @return array|ProductVariantInterface[]
     */
    public function findByCodes(array $codes): array;

    public function findOneByIdAndProductId($id, $productId): ?ProductVariantInterface;

    /**
     * @return array|ProductVariantInterface[]
     */
    public function findByPhraseAndProductCode(string $phrase, string $locale, string $productCode): array;

    /**
     * @return array|ProductVariantInterface[]
     */
    public function findByPhrase(string $phrase, string $locale, ?int $limit = null): array;

    /**
     * @return array|string[]
     */
    public function getCodesOfAllVariants(): array;
}
