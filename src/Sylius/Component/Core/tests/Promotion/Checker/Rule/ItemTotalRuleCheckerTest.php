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
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\ItemTotalRuleChecker;
use Sylius\Component\Promotion\Checker\Comparison\ComparisonOperatorMatcher;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(ItemTotalRuleChecker::class)]
final class ItemTotalRuleCheckerTest extends TestCase
{
    private ChannelInterface&MockObject $channel;

    private MockObject&OrderInterface $order;

    private ItemTotalRuleChecker $ruleChecker;

    protected function setUp(): void
    {
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->ruleChecker = new ItemTotalRuleChecker(new ComparisonOperatorMatcher());
    }

    public function testShouldImplementRuleCheckerInterface(): void
    {
        $this->assertInstanceOf(RuleCheckerInterface::class, $this->ruleChecker);
    }

    public function testShouldRemainInstantiableWithoutDependencies(): void
    {
        self::assertInstanceOf(ItemTotalRuleChecker::class, new ItemTotalRuleChecker());
    }

    public function testShouldReturnFalseIfThereIsNoConfigurationForOrderChannel(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');

        $this->assertFalse($this->ruleChecker->isEligible($this->order, []));
    }

    public function testShouldThrowExceptionIfPromotionSubjectIsNotOrder(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->ruleChecker->isEligible($this->createMock(PromotionSubjectInterface::class), []);
    }

    /** Legacy BC: no comparison_operator in configuration defaults to >= */
    public function testShouldRecognizeSubjectAsEligibleWhenTotalIsGreaterThanConfiguredWithLegacyOperator(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->order->expects($this->once())->method('getPromotionSubjectTotal')->willReturn(600);

        $this->assertTrue($this->ruleChecker->isEligible($this->order, ['WEB_US' => ['amount' => 500]]));
    }

    /** Legacy BC: no comparison_operator in configuration defaults to >= */
    public function testShouldRecognizeSubjectAsNotEligibleWhenTotalIsLessThanConfiguredWithLegacyOperator(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->order->expects($this->once())->method('getPromotionSubjectTotal')->willReturn(400);

        $this->assertFalse($this->ruleChecker->isEligible($this->order, ['WEB_US' => ['amount' => 500]]));
    }

    /** @return iterable<string, array{int, string, int, bool}> */
    public static function provideComparisonOperatorCases(): iterable
    {
        yield 'greater_than_equal eligible (600 >= 500)' => [600, '>=', 500, true];
        yield 'greater_than_equal not eligible (400 >= 500)' => [400, '>=', 500, false];
        yield 'greater_than_equal eligible on equal (500 >= 500)' => [500, '>=', 500, true];

        yield 'equal eligible (500 === 500)' => [500, '===', 500, true];
        yield 'equal not eligible (400 === 500)' => [400, '===', 500, false];

        yield 'different eligible (400 !== 500)' => [400, '!==', 500, true];
        yield 'different not eligible (500 !== 500)' => [500, '!==', 500, false];

        yield 'lower_than eligible (400 < 500)' => [400, '<', 500, true];
        yield 'lower_than not eligible (500 < 500)' => [500, '<', 500, false];
        yield 'lower_than not eligible (600 < 500)' => [600, '<', 500, false];

        yield 'lower_than_equal eligible (400 <= 500)' => [400, '<=', 500, true];
        yield 'lower_than_equal eligible on equal (500 <= 500)' => [500, '<=', 500, true];
        yield 'lower_than_equal not eligible (600 <= 500)' => [600, '<=', 500, false];

        yield 'greater_than eligible (600 > 500)' => [600, '>', 500, true];
        yield 'greater_than not eligible on equal (500 > 500)' => [500, '>', 500, false];
        yield 'greater_than not eligible (400 > 500)' => [400, '>', 500, false];

        yield 'unknown operator returns false' => [600, 'UNKNOWN', 500, false];
    }

    #[DataProvider('provideComparisonOperatorCases')]
    public function testShouldEvaluateComparisonOperatorCorrectly(
        int $subjectTotal,
        string $operator,
        int $amount,
        bool $expectedResult,
    ): void {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->order->expects($this->once())->method('getPromotionSubjectTotal')->willReturn($subjectTotal);

        $result = $this->ruleChecker->isEligible($this->order, [
            'WEB_US' => ['amount' => $amount, 'comparison_operator' => $operator],
        ]);

        $this->assertSame($expectedResult, $result);
    }
}
