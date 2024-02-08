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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private string $locale = 'en_US',
    ) {
    }

    /**
     * @Transform /^product(?:|s) "([^"]+)"$/
     * @Transform /^"([^"]+)" product(?:|s)$/
     * @Transform /^(?:a|an) "([^"]+)"$/
     * @Transform :product
     */
    public function getProductByName($productName)
    {
        $products = $this->productRepository->findByName($productName, $this->locale);

        Assert::eq(
            count($products),
            1,
            sprintf('%d products has been found with name "%s".', count($products), $productName),
        );

        return $products[0];
    }

    /**
     * @Transform /^products "([^"]+)" and "([^"]+)"$/
     * @Transform /^products "([^"]+)", "([^"]+)" and "([^"]+)"$/
     */
    public function getProductsByNames(...$productsNames)
    {
        return array_map(fn ($productName) => $this->getProductByName($productName), $productsNames);
    }
}
