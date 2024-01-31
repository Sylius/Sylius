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
use SM\StateMachine\StateMachine as WinzouStateMachine;
use Sylius\Abstraction\StateMachine\Exception\StateMachineExecutionException;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\WinzouStateMachineAdapter;

final class WinzouStateMachineAdapterTest extends TestCase
{
    /** @var FactoryInterface&MockObject */
    private FactoryInterface $winzouStateMachineFactory;

    /** @var WinzouStateMachine&MockObject */
    private WinzouStateMachine $winzouStateMachine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->winzouStateMachine = $this->createMock(WinzouStateMachine::class);
        $this->setStateMachineConfig($this->winzouStateMachine, [
            'transitions' => [
                'transition' => [
                    'from' => ['from_state'],
                    'to' => 'to_state',
                ],
                'another_transition' => [
                    'from' => ['another_from_state'],
                    'to' => 'another_to_state',
                ],
            ],
        ]);

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
        $subject = new \stdClass();
        $graphName = 'graph_name';

        $this->winzouStateMachine->method('can')->willReturnMap([
            ['transition', true],
            ['another_transition', false],
        ]);

        $transitions = $this->createTestSubject()->getEnabledTransitions($subject, $graphName);

        $this->assertCount(1, $transitions);
        $this->assertSame('transition', $transitions[0]->getName());
        $this->assertSame(['from_state'], $transitions[0]->getFroms());
        $this->assertSame(['to_state'], $transitions[0]->getTos());
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
        $this->setStateMachineConfig($this->winzouStateMachine, [
            'transitions' => [
                'transition_to_state' => [
                    'from' => ['from_state'],
                    'to' => 'to_state',
                ],
            ],
        ]);

        $this->winzouStateMachine->method('can')->willReturn(true);

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
        $this->setStateMachineConfig($this->winzouStateMachine, [
            'transitions' => [
                'transition_from_state' => [
                    'from' => ['from_state'],
                    'to' => 'to_state',
                ],
            ],
        ]);

        $this->winzouStateMachine->method('can')->willReturn(true);

        $this->winzouStateMachineFactory = $this->createMock(FactoryInterface::class);
        $this->winzouStateMachineFactory->method('get')->willReturn($this->winzouStateMachine);

        $stateMachine = $this->createTestSubject();

        $this->assertSame(
            'transition_from_state',
            $stateMachine->getTransitionFromState(new \stdClass(), 'graph_name', 'from_state'),
        );
    }

    private function createTestSubject(): StateMachineInterface
    {
        return new WinzouStateMachineAdapter($this->winzouStateMachineFactory);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function setStateMachineConfig(WinzouStateMachine $stateMachine, array $config): void
    {
        $reflection = new \ReflectionClass($stateMachine);
        $configProperty = $reflection->getProperty('config');
        $configProperty->setAccessible(true);
        $configProperty->setValue($stateMachine, $config);
    }
}
