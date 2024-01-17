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

namespace Tests\Sylius\Abstraction\StateMachine\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SM\Factory\FactoryInterface;
use SM\SMException;
use SM\StateMachine\StateMachineInterface as WinzouStateMachineInterface;
use Sylius\Abstraction\StateMachine\Exception\StateMachineExecutionException;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\WinzouStateMachineAdapter;
use Sylius\Component\Resource\StateMachine\StateMachineInterface as ResourceStateMachineInterface;

final class WinzouStateMachineAdapterTest extends TestCase
{
    /** @var FactoryInterface&MockObject */
    private FactoryInterface $winzouStateMachineFactory;

    /** @var WinzouStateMachineInterface&MockObject */
    private WinzouStateMachineInterface $winzouStateMachine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->winzouStateMachine = $this->createMock(WinzouStateMachineInterface::class);

        $this->winzouStateMachineFactory = $this->createMock(FactoryInterface::class);
        $this->winzouStateMachineFactory
            ->method('get')
            ->willReturn($this->winzouStateMachine)
        ;
    }

    public function testItReturnWhetherTransitionCanBeApplied(): void
    {
        $this->winzouStateMachine->method('can')->willReturn(true);

        $subject = new \stdClass();
        $graphName = 'graph_name';
        $transition = 'transition';

        $this->assertTrue($this->createTestSubject()->can($subject, $graphName, $transition));
    }

    public function testItAppliesTransition(): void
    {
        $this->winzouStateMachine->expects($this->once())->method('apply');

        $subject = new \stdClass();
        $graphName = 'graph_name';
        $transition = 'transition';

        $this->createTestSubject()->apply($subject, $graphName, $transition);
    }

    public function testItReturnsEnabledTransitions(): void
    {
        $this->winzouStateMachine->method('getPossibleTransitions')->willReturn(['transition']);

        $subject = new \stdClass();
        $graphName = 'graph_name';

        $transitions = $this->createTestSubject()->getEnabledTransitions($subject, $graphName);

        $this->assertCount(1, $transitions);
        $this->assertSame('transition', $transitions[0]->getName());
        $this->assertNull($transitions[0]->getFroms());
        $this->assertNull($transitions[0]->getTos());
    }

    public function testItConvertsWorkflowExceptionsToCustomOnesOnCan(): void
    {
        $this->expectException(StateMachineExecutionException::class);

        $subject = new \stdClass();
        $graphName = 'graph_name';
        $transition = 'transition';

        $this->winzouStateMachineFactory->method('get')->willThrowException(new SMException());

        $this->createTestSubject()->can($subject, $graphName, $transition);
    }

    public function testItConvertsWorkflowExceptionsToCustomOnApply(): void
    {
        $this->expectException(StateMachineExecutionException::class);

        $subject = new \stdClass();
        $graphName = 'graph_name';
        $transition = 'transition';

        $this->winzouStateMachineFactory->method('get')->willThrowException(new SMException());

        $this->createTestSubject()->apply($subject, $graphName, $transition);
    }

    public function testItConvertsWorkflowExceptionsToCustomOnGetEnabledTransitions(): void
    {
        $this->expectException(StateMachineExecutionException::class);

        $subject = new \stdClass();
        $graphName = 'graph_name';

        $this->winzouStateMachineFactory->method('get')->willThrowException(new SMException());

        $this->createTestSubject()->getEnabledTransitions($subject, $graphName);
    }

    public function testItReturnsTransitionsToForGivenTransition(): void
    {
        $this->winzouStateMachine = $this->createMock(ResourceStateMachineInterface::class);
        $this->winzouStateMachine->method('getTransitionToState')->willReturn('transition_to_state');

        $this->winzouStateMachineFactory = $this->createMock(FactoryInterface::class);
        $this->winzouStateMachineFactory->method('get')->willReturn($this->winzouStateMachine);

        $stateMachine = $this->createTestSubject();

        $this->assertSame(
            'transition_to_state',
            $stateMachine->getTransitionToState(new \stdClass(), 'graph_name', 'to_state'),
        );
    }

    public function testItReturnsTransitionsFromForGivenTransition(): void
    {
        $this->winzouStateMachine = $this->createMock(ResourceStateMachineInterface::class);
        $this->winzouStateMachine->method('getTransitionFromState')->willReturn('transition_from_state');

        $this->winzouStateMachineFactory = $this->createMock(FactoryInterface::class);
        $this->winzouStateMachineFactory->method('get')->willReturn($this->winzouStateMachine);

        $stateMachine = $this->createTestSubject();

        $this->assertSame(
            'transition_from_state',
            $stateMachine->getTransitionFromState(new \stdClass(), 'graph_name', 'to_state'),
        );
    }

    private function createTestSubject(): StateMachineInterface
    {
        return new WinzouStateMachineAdapter($this->winzouStateMachineFactory);
    }
}
