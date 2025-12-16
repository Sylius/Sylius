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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\NthOrderRuleChecker;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;

final class NthOrderRuleCheckerTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $ordersRepository;

    private CustomerInterface&MockObject $customer;

    private MockObject&OrderInterface $subject;

    private NthOrderRuleChecker $ruleChecker;

    protected function setUp(): void
    {
        $this->ordersRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->customer = $this->createMock(CustomerInterface::class);
        $this->subject = $this->createMock(OrderInterface::class);
        $this->ruleChecker = new NthOrderRuleChecker($this->ordersRepository);
    }

    public function testShouldImplementRuleCheckerInterface(): void
    {
        $this->assertInstanceOf(RuleCheckerInterface::class, $this->ruleChecker);
    }

    public function testShouldRecognizeSubjectWithoutCustomerAsNotEligible(): void
    {
        $this->subject->expects($this->once())->method('getCustomer')->willReturn(null);

        $this->assertFalse($this->ruleChecker->isEligible($this->subject, ['nth' => 10]));
    }

    public function testShouldRecognizeSubjectAsNotEligibleIfNthOrderIsZero(): void
    {
        $this->subject->expects($this->once())->method('getCustomer')->willReturn($this->customer);
        $this->customer->expects($this->once())->method('getId')->willReturn(1);
        $this->ordersRepository->expects($this->once())->method('countByCustomer')->with($this->customer)->willReturn(0);

        $this->assertFalse($this->ruleChecker->isEligible($this->subject, ['nth' => 10]));
    }

    public function testShouldRecognizeSubjectAsNotEligibleIfNthOrderIsLessThenConfigured(): void
    {
        $this->subject->expects($this->once())->method('getCustomer')->willReturn($this->customer);
        $this->customer->expects($this->once())->method('getId')->willReturn(1);
        $this->ordersRepository->expects($this->once())->method('countByCustomer')->with($this->customer)->willReturn(5);

        $this->assertFalse($this->ruleChecker->isEligible($this->subject, ['nth' => 10]));
    }

    public function testShouldRecognizeSubjectAsNotEligibleIfNthOrderIsGreaterThanConfigured(): void
    {
        $this->subject->expects($this->once())->method('getCustomer')->willReturn($this->customer);
        $this->customer->expects($this->once())->method('getId')->willReturn(1);
        $this->ordersRepository->expects($this->once())->method('countByCustomer')->with($this->customer)->willReturn(12);

        $this->assertFalse($this->ruleChecker->isEligible($this->subject, ['nth' => 10]));
    }

    public function testShouldRecognizeSubjectAsEligibleIfNthOrderIsEqualWithConfigured(): void
    {
        $this->subject->expects($this->once())->method('getCustomer')->willReturn($this->customer);
        $this->customer->expects($this->once())->method('getId')->willReturn(1);
        $this->ordersRepository->expects($this->once())->method('countByCustomer')->with($this->customer)->willReturn(9);

        $this->assertTrue($this->ruleChecker->isEligible($this->subject, ['nth' => 10]));
    }

    public function testShouldRecognizeSubjectAsEligibleIfNthOrderIsOneAndCustomerIsNotInDatabase(): void
    {
        $this->subject->expects($this->once())->method('getCustomer')->willReturn($this->customer);
        $this->customer->expects($this->once())->method('getId')->willReturn(null);

        $this->assertTrue($this->ruleChecker->isEligible($this->subject, ['nth' => 1]));
    }

    public function testShouldRecognizeSubjectAsNotEligibleIfItIsFirstOrderOfNewCustomerAndPromotionIsForMoreThanOneOrder(): void
    {
        $this->subject->expects($this->once())->method('getCustomer')->willReturn($this->customer);
        $this->customer->expects($this->once())->method('getId')->willReturn(null);

        $this->assertFalse($this->ruleChecker->isEligible($this->subject, ['nth' => 10]));
    }

    public function testShouldRecognizeSubjectAsNotEligibleIfConfigurationIsInvalid(): void
    {
        $this->assertFalse($this->ruleChecker->isEligible($this->subject, []));
    }

    public function testShouldThrowExceptionIfSubjectIsNotOrder(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->ruleChecker->isEligible($this->createMock(PromotionSubjectInterface::class), ['nth' => 10]);
    }
}
