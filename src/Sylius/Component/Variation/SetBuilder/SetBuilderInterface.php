<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Variation\SetBuilder;

/**
 * Build a product set from one or more given sets.
 *
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
interface SetBuilderInterface
{
    /**
     * Get all permutations of option set.
     *
     * @param array   $setTuples
     * @param bool $isRecursiveStep
     *
     * @return array The product set of tuples.
     */
    public function build(array $setTuples, $isRecursiveStep = false);
}
