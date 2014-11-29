<?php

namespace Sylius\Component\Variation\Generator;

class CartesianSetBuilder implements SetBuilderInterface
{
    /**
     * Get all permutations of option set.
     * Cartesian product.
     *
     * @param array[] $setTuples
     * @param boolean $isRecursiveStep
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function build(array $setTuples, $isRecursiveStep = false)
    {
        $countArrays = count($setTuples);

        if (1 === $countArrays) {
            return reset($setTuples);
        } elseif (0 === $countArrays) {
            throw new \InvalidArgumentException('The set builder requires a single array of one or more array sets.');
        }

        foreach ($setTuples as $tuple) {
            if (!is_array($tuple)) {
                throw new \InvalidArgumentException('The set builder requires a single array of one or more array sets.');
            }
        }

        $keys = array_keys($setTuples);

        $a = array_shift($setTuples);
        $k = array_shift($keys);

        $b = $this->build($setTuples, true);

        $result = array();

        foreach ($a as $valueA) {
            if ($valueA) {
                foreach ($b as $valueB) {
                    if ($isRecursiveStep) {
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
