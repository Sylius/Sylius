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

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;

/**
 * Variant generator service implementation.
 *
 * It is used to create all possible combinations of object options
 * and create Variant models from them.
 *
 * Example:
 *
 * If object has two options with 3 possible values each,
 * this service will create 9 Variant's and assign them to the
 * object. It ignores existing and invalid variants.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ProductVariantGenerator implements ProductVariantGeneratorInterface
{
    /**
     * @var FactoryInterface
     */
    protected $variantFactory;

    /**
     * @var CartesianSetBuilder
     */
    private $setBuilder;

    /**
     * @param FactoryInterface $variantFactory
     */
    public function __construct(FactoryInterface $variantFactory)
    {
        $this->variantFactory = $variantFactory;
        $this->setBuilder = new CartesianSetBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ProductInterface $variable)
    {
        if (!$variable->hasOptions()) {
            throw new \InvalidArgumentException('Cannot generate variants for an object without options.');
        }

        $optionSet = [];
        $optionMap = [];

        foreach ($variable->getOptions() as $key => $option) {
            foreach ($option->getValues() as $value) {
                $optionSet[$key][] = $value->getId();
                $optionMap[$value->getId()] = $value;
            }
        }

        $permutations = $this->setBuilder->build($optionSet);

        foreach ($permutations as $permutation) {
            $variant = $this->createVariant($variable, $optionMap, $permutation);
            $variable->addVariant($variant);
        }
    }

    /**
     * @param ProductInterface $variable
     * @param array $optionMap
     * @param mixed $permutation
     *
     * @return ProductVariantInterface
     */
    protected function createVariant(ProductInterface $variable, array $optionMap, $permutation)
    {
        $variant = $this->variantFactory->createNew();
        $variant->setProduct($variable);

        if (is_array($permutation)) {
            foreach ($permutation as $id) {
                $variant->addOptionValue($optionMap[$id]);
            }
        } else {
            $variant->addOptionValue($optionMap[$permutation]);
        }

        return $variant;
    }
}
