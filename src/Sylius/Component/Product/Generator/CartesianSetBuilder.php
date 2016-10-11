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

use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class CartesianSetBuilder
{
    /**
     * @param array[] $set
     *
     * @return array
     */
    public function build(array $set)
    {
        Assert::isArray($set);
        Assert::allIsArray($set);

        if (!$set) {
            return [[]];
        }

        $subset = (array) array_shift($set);
        $cartesianSubset = $this->build($set);

        $result = [];
        foreach ($subset as $value) {
            foreach ($cartesianSubset as $cartesianValue) {
                $result[] = array_merge([$value], $cartesianValue);
            }
        }

        return $result;
    }
}
