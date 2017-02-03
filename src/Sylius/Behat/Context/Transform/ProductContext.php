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
use Webmozart\Assert\Assert;

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
     * @Transform /^product(?:|s) "([^"]+)"$/
     * @Transform /^"([^"]+)" product(?:|s)$/
     * @Transform /^(?:a|an) "([^"]+)"$/
     * @Transform :product
     */
    public function getProductByName($productName)
    {
        $products = $this->productRepository->findByName($productName, 'en_US');

        Assert::eq(
            count($products),
            1,
            sprintf('%d products has been found with name "%s".', count($products), $productName)
        );

        return $products[0];
    }

    /**
     * @Transform /^products "([^"]+)" and "([^"]+)"$/
     * @Transform /^products "([^"]+)", "([^"]+)" and "([^"]+)"$/
     */
    public function getProductsByNames(...$productsNames)
    {
        return array_map(function ($productName) {
            return $this->getProductByName($productName);
        }, $productsNames);
    }
}
