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
    public function __construct(ProductRepositoryInterface $productRepository, ProductVariantRepositoryInterface $productVariantRepository)
    {
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
    }

    /**
     * @Transform /^"([^"]+)" variant of product "([^"]+)"$/
     */
    public function getProductVariantByNameAndProduct($variantName, $productName)
    {
        $product = $this->productRepository->findOneByName($productName);
        if (null === $product) {
            throw new \InvalidArgumentException(sprintf('Product with name "%s" does not exist', $productName));
        }

        $productVariant = $this->productVariantRepository->findOneBy(['name' => $variantName, 'object' => $product]);
        if (null === $productVariant) {
            throw new \InvalidArgumentException(sprintf('Product variant with name "%s" of product "%s" does not exist', $variantName, $productName));
        }

        return $productVariant;
    }

    /**
     * @Transform /^"([^"]+)" product variant$/
     */
    public function getProductVariantByName($name)
    {
        $productVariant = $this->productVariantRepository->findOneBy(['name' => $name]);
        Assert::notNull($productVariant, sprintf('There is no product variant for "%s" name', $name));

        return $productVariant;
    }
}
