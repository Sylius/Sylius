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

namespace Tests\Sylius\Component\Core\Promotion\Checker\Rule;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Promotion\Checker\Rule\CartQuantityRuleChecker;
use Sylius\Component\Promotion\Checker\Comparison\ComparisonOperatorMatcher;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\CountablePromotionSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(CartQuantityRuleChecker::class)]
final class CartQuantityRuleCheckerTest extends TestCase
{
    private CartQuantityRuleChecker $ruleChecker;

    protected function setUp(): void
    {
        $this->ruleChecker = new CartQuantityRuleChecker(new ComparisonOperatorMatcher());
    }

    public function testShouldImplementRuleCheckerInterface(): void
    {
        $this->assertInstanceOf(RuleCheckerInterface::class, $this->ruleChecker);
    }

    public function testShouldRemainInstantiableWithoutDependencies(): void
    {
        self::assertInstanceOf(CartQuantityRuleChecker::class, new CartQuantityRuleChecker());
    }

    public function testShouldReturnFalseIfSubjectIsNotCountablePromotionSubject(): void
    {
        $subject = $this->createMock(PromotionSubjectInterface::class);

        $this->assertFalse($this->ruleChecker->isEligible($subject, ['count' => 5]));
    }

    /** Legacy BC: no comparison_operator in configuration defaults to >= */
    public function testShouldRecognizeSubjectAsEligibleWhenCountIsGreaterThanConfiguredWithLegacyOperator(): void
    {
        $subject = $this->createMockCountableSubject(6);

        $this->assertTrue($this->ruleChecker->isEligible($subject, ['count' => 5]));
    }

    /** Legacy BC: no comparison_operator in configuration defaults to >= */
    public function testShouldRecognizeSubjectAsNotEligibleWhenCountIsLessThanConfiguredWithLegacyOperator(): void
    {
        $subject = $this->createMockCountableSubject(3);

        $this->assertFalse($this->ruleChecker->isEligible($subject, ['count' => 5]));
    }

    /** @return iterable<string, array{int, string, int, bool}> */
    public static function provideComparisonOperatorCases(): iterable
    {
        yield 'greater_than_equal eligible (6 >= 5)' => [6, '>=', 5, true];
        yield 'greater_than_equal not eligible (3 >= 5)' => [3, '>=', 5, false];
        yield 'greater_than_equal eligible on equal (5 >= 5)' => [5, '>=', 5, true];

        yield 'equal eligible (5 === 5)' => [5, '===', 5, true];
        yield 'equal not eligible (3 === 5)' => [3, '===', 5, false];

        yield 'different eligible (3 !== 5)' => [3, '!==', 5, true];
        yield 'different not eligible (5 !== 5)' => [5, '!==', 5, false];

        yield 'lower_than eligible (3 < 5)' => [3, '<', 5, true];
        yield 'lower_than not eligible on equal (5 < 5)' => [5, '<', 5, false];
        yield 'lower_than not eligible (6 < 5)' => [6, '<', 5, false];

        yield 'lower_than_equal eligible (3 <= 5)' => [3, '<=', 5, true];
        yield 'lower_than_equal eligible on equal (5 <= 5)' => [5, '<=', 5, true];
        yield 'lower_than_equal not eligible (6 <= 5)' => [6, '<=', 5, false];

        yield 'greater_than eligible (6 > 5)' => [6, '>', 5, true];
        yield 'greater_than not eligible on equal (5 > 5)' => [5, '>', 5, false];
        yield 'greater_than not eligible (3 > 5)' => [3, '>', 5, false];

        yield 'unknown operator returns false' => [6, 'UNKNOWN', 5, false];
    }

    #[DataProvider('provideComparisonOperatorCases')]
    public function testShouldEvaluateComparisonOperatorCorrectly(
        int $subjectCount,
        string $operator,
        int $count,
        bool $expectedResult,
    ): void {
        $subject = $this->createMockCountableSubject($subjectCount);

        $result = $this->ruleChecker->isEligible($subject, [
            'count' => $count,
            'comparison_operator' => $operator,
        ]);

        $this->assertSame($expectedResult, $result);
    }

    private function createMockCountableSubject(int $count): CountablePromotionSubjectInterface&MockObject
    {
        $subject = $this->createMock(CountablePromotionSubjectInterface::class);
        $subject->method('getPromotionSubjectCount')->willReturn($count);

        return $subject;
    }
}
