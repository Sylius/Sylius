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

namespace Tests\Sylius\Component\Product\Generator;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Product\Generator\CartesianSetBuilder;

final class CartesianSetBuilderTest extends TestCase
{
    private CartesianSetBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new CartesianSetBuilder();
    }

    public function testRequiresAnArrayOfSetTuplesToBuildFrom(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $tupleSetNotInArray = ['a', 'b', 'c'];
        $this->builder->build($tupleSetNotInArray);
    }

    public function testRequiresAtLeastOneSetTuple(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->builder->build([]);
    }

    public function testReturnsTheSameSetAsTheCartesianProductWhenOnlyOneWasGiven(): void
    {
        $set = ['a', 'b', 'c'];
        $result = $this->builder->build([$set]);

        $this->assertSame($set, $result);
    }

    public function testBuildsTheCartesianProductSetFromTwoSets(): void
    {
        $setA = ['a', 'b', 'c'];
        $setB = ['1', '2', '3'];

        $expected = [
            ['a', '1'], ['a', '2'], ['a', '3'],
            ['b', '1'], ['b', '2'], ['b', '3'],
            ['c', '1'], ['c', '2'], ['c', '3'],
        ];

        $this->assertSame($expected, $this->builder->build([$setA, $setB]));
    }

    public function testBuildsTheCartesianProductSetFromMoreThanTwoSets(): void
    {
        $setA = ['a', 'b', 'c'];
        $setB = ['1', '2', '3'];
        $setC = ['!', '@', '$'];

        $expected = [
            ['a', '1', '!'], ['a', '1', '@'], ['a', '1', '$'],
            ['a', '2', '!'], ['a', '2', '@'], ['a', '2', '$'],
            ['a', '3', '!'], ['a', '3', '@'], ['a', '3', '$'],

            ['b', '1', '!'], ['b', '1', '@'], ['b', '1', '$'],
            ['b', '2', '!'], ['b', '2', '@'], ['b', '2', '$'],
            ['b', '3', '!'], ['b', '3', '@'], ['b', '3', '$'],

            ['c', '1', '!'], ['c', '1', '@'], ['c', '1', '$'],
            ['c', '2', '!'], ['c', '2', '@'], ['c', '2', '$'],
            ['c', '3', '!'], ['c', '3', '@'], ['c', '3', '$'],
        ];

        $this->assertSame($expected, $this->builder->build([$setA, $setB, $setC]));
    }
}
