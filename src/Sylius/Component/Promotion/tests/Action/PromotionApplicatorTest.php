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

namespace Tests\Sylius\Component\Promotion\Action;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Action\PromotionApplicator;
use Sylius\Component\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class PromotionApplicatorTest extends TestCase
{
    private MockObject&PromotionActionCommandInterface $firstPromotionActionCommand;

    private MockObject&PromotionActionCommandInterface $secondPromotionActionCommand;

    private MockObject&PromotionSubjectInterface $promotionSubject;

    private MockObject&PromotionInterface $promotion;

    private MockObject&PromotionActionInterface $firstPromotionAction;

    private MockObject&PromotionActionInterface $secondPromotionAction;

    private MockObject&ServiceRegistryInterface $serviceRegistry;

    private PromotionApplicator $promotionApplicator;

    protected function setUp(): void
    {
        $this->firstPromotionActionCommand = $this->createMock(PromotionActionCommandInterface::class);
        $this->secondPromotionActionCommand = $this->createMock(PromotionActionCommandInterface::class);
        $this->promotionSubject = $this->createMock(PromotionSubjectInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->firstPromotionAction = $this->createMock(PromotionActionInterface::class);
        $this->secondPromotionAction = $this->createMock(PromotionActionInterface::class);
        $this->serviceRegistry = $this->createMock(ServiceRegistryInterface::class);
        $this->promotionApplicator = new PromotionApplicator($this->serviceRegistry);
    }

    public function testShouldImplementPromotionApplicatorInterface(): void
    {
        $this->assertInstanceOf(PromotionApplicatorInterface::class, $this->promotionApplicator);
    }

    public function testShouldExecuteAllRegisteredActions(): void
    {
        $this->serviceRegistry->expects($this->once())->method('get')->willReturn($this->firstPromotionActionCommand);
        $this->promotion->expects($this->once())->method('getActions')->willReturn(new ArrayCollection([$this->firstPromotionAction]));
        $this->firstPromotionAction->expects($this->once())->method('getType')->willReturn('test_action');
        $this->firstPromotionAction->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->firstPromotionActionCommand
            ->expects($this->once())
            ->method('execute')
            ->with($this->promotionSubject, [], $this->promotion)
            ->willReturn(true);
        $this->promotionSubject->expects($this->once())->method('addPromotion')->with($this->promotion);

        $this->promotionApplicator->apply($this->promotionSubject, $this->promotion);
    }

    public function testShouldApplyPromotionIfAtLeastOneActionWasExecutedEvenIfTheLastOneWasNot(): void
    {
        $this->promotion
            ->expects($this->once())
            ->method('getActions')
            ->willReturn(new ArrayCollection([$this->firstPromotionAction, $this->secondPromotionAction]));
        $this->firstPromotionAction->expects($this->once())->method('getType')->willReturn('first_action');
        $this->firstPromotionAction->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->secondPromotionAction->expects($this->once())->method('getType')->willReturn('second_action');
        $this->secondPromotionAction->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->serviceRegistry->expects($this->exactly(2))->method('get')->willReturnMap([
            ['first_action', $this->firstPromotionActionCommand],
            ['second_action', $this->secondPromotionActionCommand],
        ]);
        $this->firstPromotionActionCommand
            ->expects($this->once())
            ->method('execute')
            ->with($this->promotionSubject, [], $this->promotion)
            ->willReturn(true);
        $this->secondPromotionActionCommand
            ->expects($this->once())
            ->method('execute')
            ->with($this->promotionSubject, [], $this->promotion)
            ->willReturn(false);
        $this->promotionSubject->expects($this->once())->method('addPromotion')->with($this->promotion);

        $this->promotionApplicator->apply($this->promotionSubject, $this->promotion);
    }

    public function testShouldApplyPromotionIfAtLeastOneActionWasExecuted(): void
    {
        $this->promotion
            ->expects($this->once())
            ->method('getActions')
            ->willReturn(new ArrayCollection([$this->firstPromotionAction, $this->secondPromotionAction]));
        $this->firstPromotionAction->expects($this->once())->method('getType')->willReturn('first_action');
        $this->firstPromotionAction->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->secondPromotionAction->expects($this->once())->method('getType')->willReturn('second_action');
        $this->secondPromotionAction->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->serviceRegistry->expects($this->exactly(2))->method('get')->willReturnMap([
            ['first_action', $this->firstPromotionActionCommand],
            ['second_action', $this->secondPromotionActionCommand],
        ]);
        $this->firstPromotionActionCommand
            ->expects($this->once())
            ->method('execute')
            ->with($this->promotionSubject, [], $this->promotion)
            ->willReturn(false);
        $this->secondPromotionActionCommand
            ->expects($this->once())
            ->method('execute')
            ->with($this->promotionSubject, [], $this->promotion)
            ->willReturn(true);
        $this->promotionSubject->expects($this->once())->method('addPromotion')->with($this->promotion);

        $this->promotionApplicator->apply($this->promotionSubject, $this->promotion);
    }

    public function testShouldNotAddPromotionIfNoActionHasBeenApplied(): void
    {
        $this->promotion
            ->expects($this->once())
            ->method('getActions')
            ->willReturn(new ArrayCollection([$this->firstPromotionAction, $this->secondPromotionAction]));
        $this->firstPromotionAction->expects($this->once())->method('getType')->willReturn('first_action');
        $this->firstPromotionAction->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->secondPromotionAction->expects($this->once())->method('getType')->willReturn('second_action');
        $this->secondPromotionAction->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->serviceRegistry->expects($this->exactly(2))->method('get')->willReturnMap([
            ['first_action', $this->firstPromotionActionCommand],
            ['second_action', $this->secondPromotionActionCommand],
        ]);
        $this->firstPromotionActionCommand
            ->expects($this->once())
            ->method('execute')
            ->with($this->promotionSubject, [], $this->promotion)
            ->willReturn(false);
        $this->secondPromotionActionCommand
            ->expects($this->once())
            ->method('execute')
            ->with($this->promotionSubject, [], $this->promotion)
            ->willReturn(false);
        $this->promotionSubject->expects($this->never())->method('addPromotion')->with($this->promotion);

        $this->promotionApplicator->apply($this->promotionSubject, $this->promotion);
    }

    public function testShouldRevertAllRegisteredActions(): void
    {
        $this->serviceRegistry
            ->expects($this->once())
            ->method('get')
            ->with('first_action')
            ->willReturn($this->firstPromotionActionCommand);
        $this->promotion
            ->expects($this->once())
            ->method('getActions')
            ->willReturn(new ArrayCollection([$this->firstPromotionAction]));
        $this->firstPromotionAction->expects($this->once())->method('getType')->willReturn('first_action');
        $this->firstPromotionAction->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->firstPromotionActionCommand
            ->expects($this->once())
            ->method('revert')
            ->with($this->promotionSubject, [], $this->promotion);
        $this->promotionSubject->expects($this->once())->method('removePromotion')->with($this->promotion);

        $this->promotionApplicator->revert($this->promotionSubject, $this->promotion);
    }
}
