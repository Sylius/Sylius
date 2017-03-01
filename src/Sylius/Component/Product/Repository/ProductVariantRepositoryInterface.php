<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ProductVariantRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $locale
     * @param mixed $productId
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderByProductId($locale, $productId);

    /**
     * @param string $name
     * @param string $locale
     *
     * @return ProductVariantInterface[]
     */
    public function findByName($name, $locale);

    /**
     * @param string $name
     * @param string $locale
     * @param ProductInterface $product
     *
     * @return ProductVariantInterface[]
     */
    public function findByNameAndProduct($name, $locale, ProductInterface $product);

    /**
     * @param string $productCode
     * @param string $code
     *
     * @return ProductVariantInterface|null
     */
    public function findOneByProductCodeAndCode($productCode, $code);
}
