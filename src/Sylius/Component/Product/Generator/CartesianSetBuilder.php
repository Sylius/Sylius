<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Product\Generator;

use Webmozart\Assert\Assert;

/**
 * Builds the Cartesian product set from one or more given sets.
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
    private function doBuild(array $setTuples, bool $isRecursiveStep): array
    {
        $countTuples = count($setTuples);

        if (1 === $countTuples) {
            return reset($setTuples);
        }

        $setTuples = $this->validateTuples($setTuples, $countTuples);

        $keys = array_keys($setTuples);
        $a = array_shift($setTuples);
        $k = array_shift($keys);

        $b = $this->doBuild($setTuples, true);

        $result = [];

        foreach ($a as $valueA) {
            if (!$valueA) {
                continue;
            }

            foreach ($b as $valueB) {
                $result[] = $this->getResult($isRecursiveStep, $k, $keys, $valueA, $valueB);
            }
        }

        return $result;
    }

    /**
     * @param array $setTuples
     * @param int $countTuples
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    private function validateTuples(array $setTuples, int $countTuples): array
    {
        Assert::notEq(0, $countTuples, 'The set builder requires a single array of one or more array sets.');

        foreach ($setTuples as $tuple) {
            Assert::isArray($tuple, 'The set builder requires a single array of one or more array sets.');
        }

        return $setTuples;
    }

    /**
     * @param bool $isRecursiveStep
     * @param mixed $k
     * @param array $keys
     * @param mixed $valueA
     * @param mixed $valueB
     *
     * @return array
     */
    private function getResult(bool $isRecursiveStep, $k, array $keys, $valueA, $valueB): array
    {
        if ($isRecursiveStep) {
            return array_merge([$valueA], (array) $valueB);
        }

        return [$k => $valueA] + array_combine($keys, (array) $valueB);
    }
}
