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

namespace Tests\Sylius\Component\Core\Distributor;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Distributor\IntegerDistributor;
use Sylius\Component\Core\Distributor\IntegerDistributorInterface;

final class IntegerDistributorTest extends TestCase
{
    private IntegerDistributor $distributor;

    protected function setUp(): void
    {
        $this->distributor = new IntegerDistributor();
    }

    public function testShouldImplementIntegerDistributorInterface(): void
    {
        $this->assertInstanceOf(IntegerDistributorInterface::class, $this->distributor);
    }

    /** @param int[] $expectedResults */
    #[DataProvider('provideSimpleIntegersData')]
    public function testShouldDistributeSimpleIntegers(int $amount, int $numberOfTargets, array $expectedResults): void
    {
        $this->assertSame($expectedResults, $this->distributor->distribute($amount, $numberOfTargets));
    }

    /** @return iterable<array{int, int, int[]}> */
    public static function provideSimpleIntegersData(): iterable
    {
        yield [0, 4, [0, 0, 0, 0]];
        yield [1000, 4, [250, 250, 250, 250]];
        yield [-1000, 4, [-250, -250, -250, -250]];
    }

    /** @param int[] $expectedResults */
    #[DataProvider('provideCannotSplitEquallyData')]
    public function testShouldDistributeIntegersThatCannotBeSplitEqually(int $amount, int $numberOfTargets, array $expectedResults): void
    {
        $this->assertSame($expectedResults, $this->distributor->distribute($amount, $numberOfTargets));
    }

    /** @return iterable<array{int, int, int[]}> */
    public static function provideCannotSplitEquallyData(): iterable
    {
        yield [22, 7, [4, 3, 3, 3, 3, 3, 3]];
        yield [1000, 3, [334, 333, 333]];
        yield [-1000, 3, [-334, -333, -333]];
    }

    public function testShouldThrowExceptionIfNumberOfTargetsIsBelowOne(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->distributor->distribute(1000, 0);
    }
}
