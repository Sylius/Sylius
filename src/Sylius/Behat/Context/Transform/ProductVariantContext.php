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
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
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
     * @var RepositoryInterface
     */
    private $productVariantRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param RepositoryInterface $productVariantRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository, RepositoryInterface $productVariantRepository)
    {
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
            1,
            count($products),
            sprintf('%d products has been found with name "%s".', count($products), $productName)
        );

        $productVariant = $this->productVariantRepository->findOneBy(['name' => $variantName, 'product' => $products[0]]);
        if (null === $productVariant) {
            throw new \InvalidArgumentException(sprintf('Product variant with name "%s" of product "%s" does not exist', $variantName, $productName));
        }

        return $productVariant;
    }

    /**
     * @Transform /^"([^"]+)" product variant$/
     * @Transform /^"([^"]+)" variant$/
     * @Transform :variant
     */
    public function getProductVariantByName($name)
    {
        $productVariants = $this->productVariantRepository->findByName($name);

        Assert::eq(
            1,
            count($productVariants),
            sprintf('%d product variants has been found with name "%s".', count($productVariants), $name)
        );

        return $productVariants[0];
    }
}
