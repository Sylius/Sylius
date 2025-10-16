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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\Processor;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\CatalogPromotionEligibilityCheckerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionStateProcessor;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionStateProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;

final class CatalogPromotionStateProcessorTest extends TestCase
{
    private CatalogPromotionEligibilityCheckerInterface&MockObject $catalogPromotionEligibilityChecker;

    private MockObject&StateMachineInterface $stateMachine;

    private CatalogPromotionStateProcessorInterface $processor;

    protected function setUp(): void
    {
        $this->catalogPromotionEligibilityChecker = $this->createMock(CatalogPromotionEligibilityCheckerInterface::class);
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->processor = new CatalogPromotionStateProcessor(
            $this->catalogPromotionEligibilityChecker,
            $this->stateMachine,
        );
    }

    public function testImplementsCatalogPromotionStateProcessorInterface(): void
    {
        $this->assertInstanceOf(CatalogPromotionStateProcessorInterface::class, $this->processor);
    }

    public function testProcessesCatalogPromotion(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $this->catalogPromotionEligibilityChecker
            ->method('isCatalogPromotionEligible')
            ->with($catalogPromotion)
            ->willReturn(true)
        ;

        $this->stateMachine
            ->method('can')
            ->willReturnMap([
                [$catalogPromotion, CatalogPromotionTransitions::GRAPH, CatalogPromotionTransitions::TRANSITION_PROCESS, true],
                [$catalogPromotion, CatalogPromotionTransitions::GRAPH, CatalogPromotionTransitions::TRANSITION_ACTIVATE, false],
                [$catalogPromotion, CatalogPromotionTransitions::GRAPH, CatalogPromotionTransitions::TRANSITION_DEACTIVATE, false],
            ])
        ;

        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with(
                $catalogPromotion,
                CatalogPromotionTransitions::GRAPH,
                CatalogPromotionTransitions::TRANSITION_PROCESS,
            )
        ;

        $this->processor->process($catalogPromotion);
    }

    public function testActivatesCatalogPromotion(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $this->catalogPromotionEligibilityChecker
            ->method('isCatalogPromotionEligible')
            ->with($catalogPromotion)
            ->willReturn(true)
        ;

        $this->stateMachine
            ->method('can')
            ->willReturnMap([
                [$catalogPromotion, CatalogPromotionTransitions::GRAPH, CatalogPromotionTransitions::TRANSITION_PROCESS, false],
                [$catalogPromotion, CatalogPromotionTransitions::GRAPH, CatalogPromotionTransitions::TRANSITION_ACTIVATE, true],
                [$catalogPromotion, CatalogPromotionTransitions::GRAPH, CatalogPromotionTransitions::TRANSITION_DEACTIVATE, false],
            ])
        ;

        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with(
                $catalogPromotion,
                CatalogPromotionTransitions::GRAPH,
                CatalogPromotionTransitions::TRANSITION_ACTIVATE,
            )
        ;

        $this->processor->process($catalogPromotion);
    }

    public function testDeactivatesCatalogPromotion(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $this->catalogPromotionEligibilityChecker
            ->method('isCatalogPromotionEligible')
            ->with($catalogPromotion)
            ->willReturn(false)
        ;

        $this->stateMachine
            ->method('can')
            ->willReturnMap([
                [$catalogPromotion, CatalogPromotionTransitions::GRAPH, CatalogPromotionTransitions::TRANSITION_PROCESS, false],
                [$catalogPromotion, CatalogPromotionTransitions::GRAPH, CatalogPromotionTransitions::TRANSITION_ACTIVATE, false],
                [$catalogPromotion, CatalogPromotionTransitions::GRAPH, CatalogPromotionTransitions::TRANSITION_DEACTIVATE, true],
            ])
        ;

        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($catalogPromotion, CatalogPromotionTransitions::GRAPH, CatalogPromotionTransitions::TRANSITION_DEACTIVATE)
        ;

        $this->processor->process($catalogPromotion);
    }
}
