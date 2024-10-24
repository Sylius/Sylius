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

namespace Sylius\Component\Product\Generator;

use Sylius\Component\Product\Checker\ProductVariantsParityCheckerInterface;
use Sylius\Component\Product\Exception\ProductWithoutOptionsException;
use Sylius\Component\Product\Exception\ProductWithoutOptionsValuesException;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;

final class ProductVariantGenerator implements ProductVariantGeneratorInterface
{
    private CartesianSetBuilder $setBuilder;

    public function __construct(
        private ProductVariantFactoryInterface $productVariantFactory,
        private ProductVariantsParityCheckerInterface $variantsParityChecker,
    ) {
        $this->setBuilder = new CartesianSetBuilder();
    }

    public function generate(ProductInterface $product): void
    {
        if (!$product->hasOptions()) {
            throw new ProductWithoutOptionsException();
        }

        $optionSet = [];
        $optionMap = [];

        foreach ($product->getOptions() as $key => $option) {
            foreach ($option->getValues() as $value) {
                $optionSet[$key][] = $value->getCode();
                $optionMap[$value->getCode()] = $value;
            }
        }

        if (empty($optionSet)) {
            throw new ProductWithoutOptionsValuesException();
        }

        $permutations = $this->setBuilder->build($optionSet);

        foreach ($permutations as $permutation) {
            $variant = $this->createVariant($product, $optionMap, $permutation);

            if (!$this->variantsParityChecker->checkParity($variant, $product)) {
                $product->addVariant($variant);
            }
        }
    }

    private function createVariant(ProductInterface $product, array $optionMap, $permutation): ProductVariantInterface
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantFactory->createForProduct($product);
        $this->addOptionValue($variant, $optionMap, $permutation);

        return $variant;
    }

    private function addOptionValue(ProductVariantInterface $variant, array $optionMap, $permutation): void
    {
        if (!is_array($permutation)) {
            $variant->addOptionValue($optionMap[$permutation]);

            return;
        }

        foreach ($permutation as $code) {
            $variant->addOptionValue($optionMap[$code]);
        }
    }
}
