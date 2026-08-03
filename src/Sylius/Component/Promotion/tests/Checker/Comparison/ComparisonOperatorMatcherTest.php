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

namespace Tests\Sylius\Component\Promotion\Checker\Comparison;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Promotion\Checker\Comparison\ComparisonOperatorMatcher;
use Sylius\Component\Promotion\Checker\Comparison\ComparisonOperatorMatcherInterface;

#[CoversClass(ComparisonOperatorMatcher::class)]
final class ComparisonOperatorMatcherTest extends TestCase
{
    private ComparisonOperatorMatcher $comparisonOperatorMatcher;

    protected function setUp(): void
    {
        $this->comparisonOperatorMatcher = new ComparisonOperatorMatcher();
    }

    public function testShouldImplementComparisonOperatorMatcherInterface(): void
    {
        $this->assertInstanceOf(ComparisonOperatorMatcherInterface::class, $this->comparisonOperatorMatcher);
    }

    public function testShouldReturnDefaultComparisonOperator(): void
    {
        $this->assertSame('>=', $this->comparisonOperatorMatcher->getDefaultComparisonOperator());
    }

    /** @return iterable<string, array{int, int, string, bool}> */
    public static function provideMatchingCases(): iterable
    {
        yield 'greater_than_equal true' => [6, 5, '>=', true];
        yield 'greater_than_equal false' => [4, 5, '>=', false];

        yield 'equal true' => [5, 5, '===', true];
        yield 'equal false' => [4, 5, '===', false];

        yield 'different true' => [4, 5, '!==', true];
        yield 'different false' => [5, 5, '!==', false];

        yield 'lower_than true' => [4, 5, '<', true];
        yield 'lower_than false' => [6, 5, '<', false];

        yield 'lower_than_equal true by equality' => [5, 5, '<=', true];
        yield 'lower_than_equal false' => [6, 5, '<=', false];

        yield 'greater_than true' => [6, 5, '>', true];
        yield 'greater_than false' => [5, 5, '>', false];

        yield 'unknown operator false' => [6, 5, 'custom', false];
    }

    #[DataProvider('provideMatchingCases')]
    public function testShouldMatchUsingConfiguredOperator(
        int $subjectValue,
        int $configuredValue,
        string $comparisonOperator,
        bool $expectedResult,
    ): void {
        $this->assertSame(
            $expectedResult,
            $this->comparisonOperatorMatcher->match($subjectValue, $configuredValue, $comparisonOperator),
        );
    }

    public function testShouldReturnAvailableComparisonOperatorsMap(): void
    {
        $this->assertSame(
            [
                'greater_than_equal' => '>=',
                'equal' => '===',
                'different' => '!==',
                'lower_than' => '<',
                'lower_than_equal' => '<=',
                'greater_than' => '>',
            ],
            $this->comparisonOperatorMatcher->getAvailableComparisonOperators(),
        );
    }
}
