<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ProductVariantContext implements Context
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductVariantRepositoryInterface
     */
    private $productVariantRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param ProductVariantRepositoryInterface $productVariantRepository
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductVariantRepositoryInterface $productVariantRepository
    ) {
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
    }

    /**
     * @Transform /^"([^"]+)" variant of product "([^"]+)"$/
     */
    public function getProductVariantByNameAndProduct($variantName, $productName)
    {
        $products = $this->productRepository->findByName($productName, 'en_US');

        Assert::eq(
            count($products),
            1,
            sprintf('%d products has been found with name "%s".', count($products), $productName)
        );

        $productVariants = $this->productVariantRepository->findByNameAndProduct($variantName, 'en_US', $products[0]);
        Assert::notEmpty(
            $productVariants,
            sprintf('Product variant with name "%s" of product "%s" does not exist', $variantName, $productName)
        );

        return $productVariants[0];
    }

    /**
     * @Transform /^"([^"]+)" product variant$/
     * @Transform /^"([^"]+)" variant$/
     * @Transform :variant
     */
    public function getProductVariantByName($name)
    {
        $productVariants = $this->productVariantRepository->findByName($name, 'en_US');

        Assert::eq(
            count($productVariants),
            1,
            sprintf('%d product variants has been found with name "%s".', count($productVariants), $name)
        );

        return $productVariants[0];
    }

    /**
     * @Transform /^variant with code "([^"]+)"$/
     */
    public function getProductVariantByCode($code)
    {
        $productVariant = $this->productVariantRepository->findOneBy(['code' => $code]);

        Assert::notNull($productVariant, sprintf('Cannot find product variant with code %s', $code));

        return $productVariant;
    }
}
