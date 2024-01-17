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
use Sylius\Abstraction\StateMachine\CompositeStateMachine;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Webmozart\Assert\InvalidArgumentException;

final class CompositeStateMachineTest extends TestCase
{
    /** @var StateMachineInterface&MockObject */
    private StateMachineInterface $someStateMachineAdapter;

    /** @var StateMachineInterface&MockObject */
    private StateMachineInterface $anotherStateMachineAdapter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->someStateMachineAdapter = $this->createMock(StateMachineInterface::class);
        $this->anotherStateMachineAdapter = $this->createMock(StateMachineInterface::class);
    }

    public function testItThrowsAnExceptionIfNoStateMachineAdapterIsProvided(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one state machine adapter should be provided.');

        $this->createTestSubject(stateMachineAdapters: []);
    }

    public function testItThrowsAnExceptionIfStateMachineAdapterDoesNotImplementTheInterface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All state machine adapters should implement the "Sylius\Abstraction\StateMachine\StateMachineInterface" interface.');

        $this->createTestSubject(stateMachineAdapters: [$this->createMock(\stdClass::class)]);
    }

    public function testItUsesMappedAdapterToUseWhetherTransitionCanBeApplied(): void
    {
        $this->someStateMachineAdapter->expects($this->never())->method('can');
        $this->anotherStateMachineAdapter->expects($this->once())->method('can')->willReturn(true);

        $stateMachine = $this->createTestSubject();
        $this->assertTrue($stateMachine->can(new \stdClass(), 'another_graph', 'some_transition'));
    }

    public function testItAppliesTransitionUsingMappedAdapter(): void
    {
        $this->someStateMachineAdapter->expects($this->once())->method('apply');
        $this->anotherStateMachineAdapter->expects($this->never())->method('apply');

        $stateMachine = $this->createTestSubject();
        $stateMachine->apply(new \stdClass(), 'some_graph', 'some_transition');
    }

    public function testItReturnsEnabledTransitionsUsingMappedAdapter(): void
    {
        $this->someStateMachineAdapter->expects($this->never())->method('getEnabledTransitions');
        $this->anotherStateMachineAdapter->expects($this->once())->method('getEnabledTransitions')->willReturn(['some_transition']);

        $stateMachine = $this->createTestSubject();
        $this->assertSame(['some_transition'], $stateMachine->getEnabledTransitions(new \stdClass(), 'another_graph'));
    }

    public function testItReturnsTransitionFromStateUsingMappedAdapter(): void
    {
        $this->someStateMachineAdapter->expects($this->never())->method('getTransitionFromState');
        $this->anotherStateMachineAdapter->expects($this->once())->method('getTransitionFromState')->willReturn('some_transition');

        $stateMachine = $this->createTestSubject();
        $this->assertSame('some_transition', $stateMachine->getTransitionFromState(new \stdClass(), 'another_graph', 'some_state'));
    }

    public function testItReturnsTransitionToStateUsingMappedAdapter(): void
    {
        $this->someStateMachineAdapter->expects($this->never())->method('getTransitionToState');
        $this->anotherStateMachineAdapter->expects($this->once())->method('getTransitionToState')->willReturn('some_transition');

        $stateMachine = $this->createTestSubject();
        $this->assertSame('some_transition', $stateMachine->getTransitionToState(new \stdClass(), 'another_graph', 'some_state'));
    }

    private function createTestSubject(mixed ...$arguments): StateMachineInterface
    {
        return new CompositeStateMachine(...array_replace([
            'stateMachineAdapters' => ['some_adapter' => $this->someStateMachineAdapter, 'another_adapter' => $this->anotherStateMachineAdapter],
            'defaultAdapter' => 'some_adapter',
            'graphsToAdaptersMapping' => ['some_graph' => 'some_adapter', 'another_graph' => 'another_adapter'],
        ], $arguments));
    }
}
