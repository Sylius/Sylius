<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariableProductBundle\Generator;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\VariableProductBundle\Model\VariableProductInterface;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * Default variant generator service implementation.
 * It is used to create all possible combinations of product options
 * and create Variant models from them, directly on the product.
 *
 * Example:
 *
 * If product has two options with 3 possible values each,
 * this service will create 9 Variant's and assign them on the
 * product. It ignores existing and invalid variants.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariantGenerator implements VariantGeneratorInterface
{
    /**
     * Validator.
     *
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * Variant manager.
     *
     * @var ObjectRepository
     */
    protected $variantRepository;

    /**
     * Constructor.
     *
     * @param ValidatorInterface $validator
     * @param ObjectRepository   $variantRepository
     */
    public function __construct(ValidatorInterface $validator, ObjectRepository $variantRepository)
    {
        $this->validator = $validator;
        $this->variantRepository = $variantRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(VariableProductInterface $product)
    {
        if (!$product->hasOptions()) {
            throw new \InvalidArgumentException('Cannot generate variants for product without options');
        }

        $optionSet = array();
        $optionMap = array();

        foreach ($product->getOptions() as $k => $option) {
            foreach ($option->getValues() as $value) {
                $optionSet[$k][] = $value->getId();
                $optionMap[$value->getId()] = $value;
            }
        }

        $permutations = $this->getPermutations($optionSet);

        foreach ($permutations as $permutation) {
            $variant = $this->variantRepository->createNew();
            $variant->setProduct($product);
            $variant->setDefaults($product->getMasterVariant());

            if (is_array($permutation)) {
                foreach ($permutation as $id) {
                    $variant->addOption($optionMap[$id]);
                }
            } else {
                $variant->addOption($optionMap[$permutation]);
            }

            $product->addVariant($variant);

            if (0 < count($this->validator->validate($variant, array('sylius')))) {
                $product->removeVariant($variant);
            }
        }
    }

    /**
     * Get all permutations of option set.
     * Cartesian product.
     *
     * @param array   $array
     * @param Boolean $recursing
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function getPermutations($array, $recursing = false)
    {
        $countArrays = count($array);

        if (1 === $countArrays) {
            return reset($array);
        } elseif (0 === $countArrays) {
            throw new \InvalidArgumentException('At least one array is required.');
        }

        $keys = array_keys($array);

        $a = array_shift($array);
        $k = array_shift($keys);

        $b = $this->getPermutations($array, true);

        $result = array();

        foreach ($a as $valueA) {
            if ($valueA) {
                foreach ($b as $valueB) {
                    if ($recursing) {
                        $result[] = array_merge(array($valueA), (array) $valueB);
                    } else {
                        $result[] = array($k => $valueA) + array_combine($keys, (array) $valueB);
                    }
                }
            }
        }

        return $result;
    }
}
