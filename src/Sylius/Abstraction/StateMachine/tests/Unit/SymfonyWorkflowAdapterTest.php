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
use Sylius\Abstraction\StateMachine\Exception\StateMachineExecutionException;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\SymfonyWorkflowAdapter;
use Symfony\Component\Workflow\Exception\InvalidArgumentException;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Transition as SymfonyWorkflowTransition;
use Symfony\Component\Workflow\Workflow;

final class SymfonyWorkflowAdapterTest extends TestCase
{
    /** @var Registry&MockObject */
    private Registry $symfonyWorkflowRegistry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->symfonyWorkflowRegistry = $this->createMock(Registry::class);
    }

    public function testItReturnWhetherTransitionCanBeApplied(): void
    {
        $subject = new \stdClass();
        $graphName = 'graph_name';
        $transition = 'transition';

        $workflow = $this->createMock(Workflow::class);
        $workflow
            ->expects($this->once())
            ->method('can')
            ->with($subject, $transition)
            ->willReturn(true)
        ;

        $this->symfonyWorkflowRegistry
            ->expects($this->once())
            ->method('get')
            ->with($subject, $graphName)
            ->willReturn($workflow)
        ;

        $this->createTestSubject()->can($subject, $graphName, $transition);
    }

    public function testItAppliesTransition(): void
    {
        $subject = new \stdClass();
        $graphName = 'graph_name';
        $transition = 'transition';
        /** @var array<string, mixed> $context */
        $context = ['context'];

        $workflow = $this->createMock(Workflow::class);
        $workflow
            ->expects($this->once())
            ->method('apply')
            ->with($subject, $transition, $context)
        ;

        $this->symfonyWorkflowRegistry
            ->expects($this->once())
            ->method('get')
            ->with($subject, $graphName)
            ->willReturn($workflow)
        ;

        $this->createTestSubject()->apply($subject, $graphName, $transition, $context);
    }

    public function testItReturnsEnabledTransitions(): void
    {
        $subject = new \stdClass();
        $graphName = 'graph_name';

        $workflow = $this->createMock(Workflow::class);
        $workflow
            ->expects($this->once())
            ->method('getEnabledTransitions')
            ->with($subject)
            ->willReturn([$this->createSampleTransition()])
        ;

        $this->symfonyWorkflowRegistry
            ->expects($this->once())
            ->method('get')
            ->with($subject, $graphName)
            ->willReturn($workflow)
        ;

        $enabledTransition = $this->createTestSubject()->getEnabledTransitions($subject, $graphName);

        $this->assertCount(1, $enabledTransition);
        $this->assertSame('transition', $enabledTransition[0]->getName());
        $this->assertSame(['from'], $enabledTransition[0]->getFroms());
        $this->assertSame(['to'], $enabledTransition[0]->getTos());
    }

    public function testItConvertsWorkflowExceptionsToCustomOnesOnCan(): void
    {
        $this->expectException(StateMachineExecutionException::class);

        $subject = new \stdClass();
        $graphName = 'graph_name';
        $transition = 'transition';

        $this->symfonyWorkflowRegistry->method('get')->willThrowException(new InvalidArgumentException());

        $this->createTestSubject()->can($subject, $graphName, $transition);
    }

    public function testItConvertsWorkflowExceptionsToCustomOnApply(): void
    {
        $this->expectException(StateMachineExecutionException::class);

        $subject = new \stdClass();
        $graphName = 'graph_name';
        $transition = 'transition';

        $this->symfonyWorkflowRegistry->method('get')->willThrowException(new InvalidArgumentException());

        $this->createTestSubject()->apply($subject, $graphName, $transition);
    }

    public function testItConvertsWorkflowExceptionsToCustomOnGetEnabledTransitions(): void
    {
        $this->expectException(StateMachineExecutionException::class);

        $subject = new \stdClass();
        $graphName = 'graph_name';

        $this->symfonyWorkflowRegistry->method('get')->willThrowException(new InvalidArgumentException());

        $this->createTestSubject()->getEnabledTransitions($subject, $graphName);
    }

    private function createTestSubject(): StateMachineInterface
    {
        return new SymfonyWorkflowAdapter($this->symfonyWorkflowRegistry);
    }

    private function createSampleTransition(): SymfonyWorkflowTransition
    {
        return new SymfonyWorkflowTransition('transition', ['from'], ['to']);
    }
}
