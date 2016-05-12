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

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ProductContext implements Context
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @Transform /^product "([^"]+)"$/
     * @Transform /^"([^"]+)" product$/
     * @Transform /^"([^"]+)" products$/
     * @Transform /^(?:a|an) "([^"]+)"$/
     * @Transform :product
     */
    public function getProductByName($productName)
    {
        $product = $this->productRepository->findOneByName($productName);
        if (null === $product) {
            throw new \InvalidArgumentException(sprintf('Product with name "%s" does not exist', $productName));
        }

        return $product;
    }

    /**
     * @Transform /^products "([^"]+)" and "([^"]+)"$/
     * @Transform /^products "([^"]+)", "([^"]+)" and "([^"]+)"$/
     */
    public function getProductsByNames($firstProductName, $secondProductName, $thirdProductName = null)
    {
        $products = [
            $this->getProductByName($firstProductName),
            $this->getProductByName($secondProductName),
        ];

        if (null !== $thirdProductName) {
            $products[] = $this->getProductByName($thirdProductName);
        }

        return $products;
    }
}
