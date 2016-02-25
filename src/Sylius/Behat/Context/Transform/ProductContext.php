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
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ProductContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $productRepository;

    /**
     * @var RepositoryInterface
     */
    private $productVariantRepository;

    /**
     * @param RepositoryInterface $productRepository
     * @param RepositoryInterface $productVariantRepository
     */
    public function __construct(RepositoryInterface $productRepository, RepositoryInterface $productVariantRepository)
    {
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
    }

    /**
     * @Transform /^product "([^"]+)"$/
     * @Transform /^"([^"]+)" product$/
     * @Transform :product
     */
    public function getProductByName($productName)
    {
        $product = $this->productRepository->findOneBy(['name' => $productName]);
        if (null === $product) {
            throw new \InvalidArgumentException(sprintf('Product with name "%s" does not exist', $productName));
        }

        return $product;
    }

    /**
     * @Transform /^"([^"]+)" variant of product "([^"]+)"$/
     */
    public function getProductVariantByNameAndProduct($variantName, $productName)
    {
        $product = $this->getProductByName($productName);

        $productVariant = $this->productVariantRepository->findOneBy(['presentation' => $variantName, 'object' => $product]);
        if (null === $productVariant) {
            throw new \InvalidArgumentException(sprintf('Product variant with name "%s" of product "%s" does not exist', $variantName, $productName));
        }

        return $productVariant;
    }
}
