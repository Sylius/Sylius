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

namespace Tests\Sylius\Component\Promotion\Processor;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessor;
use Sylius\Component\Promotion\Processor\PromotionProcessorInterface;
use Sylius\Component\Promotion\Provider\PreQualifiedPromotionsProviderInterface;

final class PromotionProcessorTest extends TestCase
{
    private MockObject&PreQualifiedPromotionsProviderInterface $preQualifiedPromotionsProvider;

    private MockObject&PromotionEligibilityCheckerInterface $promotionEligibilityChecker;

    private MockObject&PromotionApplicatorInterface $promotionApplicator;

    private MockObject&PromotionSubjectInterface $promotionSubject;

    private MockObject&PromotionInterface $promotion;

    private PromotionProcessor  $processor;

    protected function setUp(): void
    {
        $this->preQualifiedPromotionsProvider = $this->createMock(PreQualifiedPromotionsProviderInterface::class);
        $this->promotionEligibilityChecker = $this->createMock(PromotionEligibilityCheckerInterface::class);
        $this->promotionApplicator = $this->createMock(PromotionApplicatorInterface::class);
        $this->promotionSubject = $this->createMock(PromotionSubjectInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->processor = new PromotionProcessor(
            $this->preQualifiedPromotionsProvider,
            $this->promotionEligibilityChecker,
            $this->promotionApplicator,
        );
    }

    public function testShouldImplementPromotionProcessorInterface(): void
    {
        $this->assertInstanceOf(PromotionProcessorInterface::class, $this->processor);
    }

    public function testShouldNotApplyPromotionsThatAreNotEligible(): void
    {
        $this->promotionSubject->expects($this->once())->method('getPromotions')->willReturn(new ArrayCollection([]));
        $this->preQualifiedPromotionsProvider
            ->expects($this->once())
            ->method('getPromotions')
            ->with(
                $this->promotionSubject,
            )->willReturn([$this->promotion]);
        $this->promotion->expects($this->exactly(2))->method('isExclusive')->willReturn(false);
        $this->promotionEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->promotionSubject, $this->promotion)
            ->willReturn(false);
        $this->promotionApplicator->expects($this->never())->method('apply')->with($this->promotionSubject, $this->promotion);
        $this->promotionApplicator->expects($this->never())->method('revert')->with($this->promotionSubject, $this->promotion);

        $this->processor->process($this->promotionSubject);
    }

    public function testShouldApplyPromotionsThatAreEligible(): void
    {
        $this->promotionSubject->expects($this->once())->method('getPromotions')->willReturn(new ArrayCollection([]));
        $this->preQualifiedPromotionsProvider
            ->expects($this->once())
            ->method('getPromotions')
            ->with($this->promotionSubject)
            ->willReturn([$this->promotion]);
        $this->promotion->expects($this->exactly(2))->method('isExclusive')->willReturn(false);
        $this->promotionEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->promotionSubject, $this->promotion)
            ->willReturn(true);
        $this->promotionApplicator->expects($this->once())->method('apply')->with($this->promotionSubject, $this->promotion);
        $this->promotionApplicator->expects($this->never())->method('revert')->with($this->promotionSubject, $this->promotion);

        $this->processor->process($this->promotionSubject);
    }

    public function testShouldApplyOnlyExclusivePromotion(): void
    {
        $exclusivePromotion = $this->createMock(PromotionInterface::class);
        $this->promotionSubject->expects($this->once())->method('getPromotions')->willReturn(new ArrayCollection([]));
        $this->preQualifiedPromotionsProvider
            ->expects($this->once())
            ->method('getPromotions')
            ->with($this->promotionSubject)
            ->willReturn([$this->promotion, $exclusivePromotion]);
        $exclusivePromotion->expects($this->once())->method('isExclusive')->willReturn(true);
        $this->promotion->expects($this->once())->method('isExclusive')->willReturn(false);
        $this->promotionEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->promotionSubject, $this->promotion)
            ->willReturn(true);
        $this->promotionEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->promotionSubject, $exclusivePromotion)
            ->willReturn(true);
        $this->promotionApplicator->expects($this->once())->method('apply')->with($this->promotionSubject, $exclusivePromotion);
        $this->promotionApplicator->expects($this->never())->method('revert')->with($this->promotionSubject, $exclusivePromotion);

        $this->processor->process($this->promotionSubject);
    }

    public function testShouldRevertPromotionsThatAreNotEligibleAnymore(): void
    {
        $this->promotionSubject
            ->expects($this->once())
            ->method('getPromotions')
            ->willReturn(new ArrayCollection([$this->promotion]));
        $this->preQualifiedPromotionsProvider->expects($this->once())->method('getPromotions')->willReturn([$this->promotion]);
        $this->promotion->expects($this->exactly(2))->method('isExclusive')->willReturn(false);
        $this->promotionEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->promotionSubject, $this->promotion)
            ->willReturn(false);
        $this->promotionApplicator->expects($this->never())->method('apply')->with($this->promotionSubject, $this->promotion);
        $this->promotionApplicator->expects($this->once())->method('revert')->with($this->promotionSubject, $this->promotion);

        $this->processor->process($this->promotionSubject);
    }
}
