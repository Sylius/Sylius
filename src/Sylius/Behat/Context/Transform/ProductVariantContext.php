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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductVariantContext implements Context
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductVariantRepositoryInterface $productVariantRepository,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Transform /^"([^"]+)" variant of product "([^"]+)"$/
     * @Transform /^"([^"]+)" variant of "([^"]+)" product$/
     */
    public function getProductVariantByNameAndProduct(string $variantName, string $productName): ProductVariantInterface
    {
        $products = $this->productRepository->findByName($productName, 'en_US');

        Assert::eq(
            count($products),
            1,
            sprintf('%d products has been found with name "%s".', count($products), $productName),
        );

        $productVariants = $this->productVariantRepository->findByNameAndProduct($variantName, 'en_US', $products[0]);
        Assert::notEmpty(
            $productVariants,
            sprintf('Product variant with name "%s" of product "%s" does not exist', $variantName, $productName),
        );

        return $productVariants[0];
    }

    /**
     * @Transform /^"([^"]+)" variant of this product$/
     */
    public function getProductVariantByNameAndThisProduct(string $variantName): ProductVariantInterface
    {
        $product = $this->sharedStorage->get('product');

        $productVariants = $this->productVariantRepository->findByNameAndProduct($variantName, 'en_US', $product);
        Assert::notEmpty(
            $productVariants,
            sprintf('Product variant with name "%s" of product "%s" does not exist', $variantName, $product->getName()),
        );

        return $productVariants[0];
    }

    /**
     * @Transform /^"([^"]+)" product variant$/
     * @Transform /^"([^"]+)" variant$/
     * @Transform /^variant "([^"]+)"$/
     * @Transform :productVariant
     * @Transform :variant
     */
    public function getProductVariantByName($name)
    {
        $productVariants = $this->productVariantRepository->findByName($name, 'en_US');

        Assert::eq(
            count($productVariants),
            1,
            sprintf('%d product variants has been found with name "%s".', count($productVariants), $name),
        );

        return $productVariants[0];
    }

    /**
     * @Transform /^"([^"]+)", "([^"]+)" and "([^"]+)" variants$/
     */
    public function getVariantsByNames(string ...$variantNames): array
    {
        return array_map(function ($variantName) {
            return $this->getProductVariantByName($variantName);
        }, $variantNames);
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

    /**
     * @Transform /^"([^"]*)" (\w+) \/ "([^"]*)" (\w+) variant of product "([^"]+)"$/
     */
    public function getVariantByOptionValuesAndProduct(
        string $value1,
        string $option1,
        string $value2,
        string $option2,
        string $productName,
    ) {
        $products = $this->productRepository->findByName($productName, 'en_US');

        Assert::eq(
            count($products),
            1,
            sprintf('%d products has been found with name "%s".', count($products), $productName),
        );
        $product = $products[0];

        $option1 = StringInflector::nameToUppercaseCode($option1);
        $option2 = StringInflector::nameToUppercaseCode($option2);
        foreach ($product->getVariants() as $variant) {
            $options = [];
            foreach ($variant->getOptionValues() as $optionValue) {
                $options[$optionValue->getOption()->getCode()] = $optionValue->getValue();
            }
            if (array_key_exists($option1, $options) && $options[$option1] === $value1 &&
                array_key_exists($option2, $options) && $options[$option2] === $value2) {
                return $variant;
            }
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Cannot find variant "%s" %s / "%s" %s within product "%s"',
                $value1,
                $option1,
                $value2,
                $option2,
                $product->getCode(),
            ),
        );
    }
}
