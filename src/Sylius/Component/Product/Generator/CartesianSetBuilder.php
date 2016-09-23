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

/**
 * Builds the Cartesian product set from one or more given sets.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class CartesianSetBuilder
{
    /**
     * @param array $setTuples
     *
     * @return array
     *
     * @throws \InvalidArgumentException If the array is empty.
     * @throws \InvalidArgumentException If the array does not contain arrays of set tuples.
     */
    public function build(array $setTuples)
    {
        return $this->doBuild($setTuples, false);
    }

    /**
     * @param array $setTuples
     * @param bool $isRecursiveStep
     *
     * @return array
     */
    private function doBuild(array $setTuples, $isRecursiveStep)
    {
        $countTuples = count($setTuples);

        if (1 === $countTuples) {
            return reset($setTuples);
        }

        if (0 === $countTuples) {
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

        $b = $this->doBuild($setTuples, true);

        $result = [];

        foreach ($a as $valueA) {
            if ($valueA) {
                foreach ($b as $valueB) {
                    if ($isRecursiveStep) {
                        $result[] = array_merge([$valueA], (array) $valueB);
                    } else {
                        $result[] = [$k => $valueA] + array_combine($keys, (array) $valueB);
                    }
                }
            }
        }

        return $result;
    }
}
