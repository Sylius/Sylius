<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Generator;

use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ProductVariantGenerator implements ProductVariantGeneratorInterface
{
    /**
     * @var ProductVariantFactoryInterface
     */
    protected $productVariantFactory;

    /**
     * @var CartesianSetBuilder
     */
    private $setBuilder;

    /**
     * @param ProductVariantFactoryInterface $productVariantFactory
     */
    public function __construct(ProductVariantFactoryInterface $productVariantFactory)
    {
        $this->productVariantFactory = $productVariantFactory;
        $this->setBuilder = new CartesianSetBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ProductInterface $product)
    {
        Assert::true($product->hasOptions(), 'Cannot generate variants for an object without options.');

        $optionSet = [];
        $optionMap = [];

        foreach ($product->getOptions() as $key => $option) {
            foreach ($option->getValues() as $value) {
                $optionSet[$key][] = $value->getId();
                $optionMap[$value->getId()] = $value;
            }
        }

        $permutations = $this->setBuilder->build($optionSet);

        foreach ($permutations as $permutation) {
            $variant = $this->createVariant($product, $optionMap, $permutation);
            $product->addVariant($variant);
        }
    }

    /**
     * @param ProductInterface $product
     * @param array $optionMap
     * @param mixed $permutation
     *
     * @return ProductVariantInterface
     */
    protected function createVariant(ProductInterface $product, array $optionMap, $permutation)
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantFactory->createForProduct($product);
        $this->addOptionValue($variant, $optionMap, $permutation);

        return $variant;
    }

    /**
     * @param ProductVariantInterface $variant
     * @param array $optionMap
     * @param mixed $permutation
     */
    private function addOptionValue(ProductVariantInterface $variant, array $optionMap, $permutation)
    {
        if (!is_array($permutation)) {
            $variant->addOptionValue($optionMap[$permutation]);

            return;
        }

        foreach ($permutation as $id) {
            $variant->addOptionValue($optionMap[$id]);
        }
    }
}
