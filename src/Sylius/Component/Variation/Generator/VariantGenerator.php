<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Variation\Generator;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Variation\Model\VariableInterface;
use Sylius\Component\Variation\Model\VariantInterface;
use Sylius\Component\Variation\SetBuilder\SetBuilderInterface;

/**
 * Abstract variant generator service implementation.
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
class VariantGenerator implements VariantGeneratorInterface
{
    /**
     * Variant manager.
     *
     * @var FactoryInterface
     */
    protected $variantFactory;

    /**
     * @var SetBuilderInterface
     */
    private $setBuilder;

    /**
     * Constructor.
     *
     * @param FactoryInterface $variantFactory
     * @param SetBuilderInterface $setBuilder
     */
    public function __construct(FactoryInterface $variantFactory, SetBuilderInterface $setBuilder)
    {
        $this->variantFactory = $variantFactory;
        $this->setBuilder = $setBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(VariableInterface $variable)
    {
        if (!$variable->hasOptions()) {
            throw new \InvalidArgumentException('Cannot generate variants for an object without options.');
        }

        $optionSet = [];
        $optionMap = [];

        foreach ($variable->getOptions() as $k => $option) {
            foreach ($option->getValues() as $value) {
                $optionSet[$k][] = $value->getId();
                $optionMap[$value->getId()] = $value;
            }
        }

        $permutations = $this->setBuilder->build($optionSet);

        foreach ($permutations as $permutation) {
            $variant = $this->variantFactory->createNew();
            $variant->setObject($variable);
            $variant->setDefaults($variable->getMasterVariant());

            if (is_array($permutation)) {
                foreach ($permutation as $id) {
                    $variant->addOption($optionMap[$id]);
                }
            } else {
                $variant->addOption($optionMap[$permutation]);
            }

            $variable->addVariant($variant);

            $this->process($variable, $variant);
        }
    }

    /**
     * Override if needed.
     *
     * @param VariableInterface $variable
     * @param VariantInterface  $variant
     */
    protected function process(VariableInterface $variable, VariantInterface $variant)
    {
    }
}
