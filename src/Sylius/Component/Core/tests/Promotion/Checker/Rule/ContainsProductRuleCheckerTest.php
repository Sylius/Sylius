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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\ContainsProductRuleChecker;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;

final class ContainsProductRuleCheckerTest extends TestCase
{
    private MockObject&OrderInterface $subject;

    private MockObject&OrderItemInterface $firstOrderItem;

    private MockObject&OrderItemInterface $secondOrderItem;

    private MockObject&ProductInterface $shaft;

    private MockObject&ProductInterface $head;

    private ContainsProductRuleChecker $ruleChecker;

    protected function setUp(): void
    {
        $this->subject = $this->createMock(OrderInterface::class);
        $this->firstOrderItem = $this->createMock(OrderItemInterface::class);
        $this->secondOrderItem = $this->createMock(OrderItemInterface::class);
        $this->shaft = $this->createMock(ProductInterface::class);
        $this->head = $this->createMock(ProductInterface::class);
        $this->ruleChecker = new ContainsProductRuleChecker();
    }

    public function testShouldImplementRuleCheckerInterface(): void
    {
        $this->assertInstanceOf(RuleCheckerInterface::class, $this->ruleChecker);
    }

    public function testShouldThrowExceptionIfThePromotionSubjectIsNotOrder(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->ruleChecker->isEligible($this->createMock(PromotionSubjectInterface::class), []);
    }

    public function testShouldReturnTrueIfProductIsRight(): void
    {
        $this->subject->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([
            $this->firstOrderItem,
            $this->secondOrderItem,
        ]));
        $this->firstOrderItem->expects($this->once())->method('getProduct')->willReturn($this->head);
        $this->secondOrderItem->expects($this->once())->method('getProduct')->willReturn($this->shaft);
        $this->head->expects($this->once())->method('getCode')->willReturn('LACROSSE_HEAD');
        $this->shaft->expects($this->once())->method('getCode')->willReturn('LACROSSE_SHAFT');

        $this->assertTrue(
            $this->ruleChecker->isEligible($this->subject, ['product_code' => 'LACROSSE_SHAFT']),
        );
    }

    public function testShouldReturnFalseIfProductIsWrong(): void
    {
        $this->subject->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([
            $this->firstOrderItem,
            $this->secondOrderItem,
        ]));
        $this->firstOrderItem->expects($this->once())->method('getProduct')->willReturn($this->head);
        $this->secondOrderItem->expects($this->once())->method('getProduct')->willReturn($this->shaft);
        $this->head->expects($this->once())->method('getCode')->willReturn('LACROSSE_HEAD');
        $this->shaft->expects($this->once())->method('getCode')->willReturn('LACROSSE_SHAFT');

        $this->assertFalse(
            $this->ruleChecker->isEligible($this->subject, ['product_code' => 'LACROSSE_STRING']),
        );
    }
}
