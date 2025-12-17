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

namespace Tests\Sylius\Bundle\CoreBundle\Resource\StateMachine\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\Resource\StateMachine\Controller\CompositeStateMachine;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Resource\Model\ResourceInterface;

final class CompositeStateMachineTest extends TestCase
{
    private MockObject&StateMachineInterface $stateMachine;

    private CompositeStateMachine $compositeStateMachine;

    protected function setUp(): void
    {
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->compositeStateMachine = new CompositeStateMachine($this->stateMachine);
    }

    public function testReturnsTrueIfCanTransition(): void
    {
        $requestConfiguration = $this->createMock(RequestConfiguration::class);
        $resource = $this->createMock(ResourceInterface::class);

        $requestConfiguration->expects($this->once())->method('hasStateMachine')->willReturn(true);
        $requestConfiguration->expects($this->once())->method('getStateMachineGraph')->willReturn('default');
        $requestConfiguration->expects($this->once())->method('getStateMachineTransition')->willReturn('transition');

        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($resource, 'default', 'transition')
            ->willReturn(true)
        ;

        $this->assertTrue($this->compositeStateMachine->can($requestConfiguration, $resource));
    }

    public function testAppliesTransition(): void
    {
        $requestConfiguration = $this->createMock(RequestConfiguration::class);
        $resource = $this->createMock(ResourceInterface::class);

        $requestConfiguration->expects($this->once())->method('hasStateMachine')->willReturn(true);
        $requestConfiguration->expects($this->once())->method('getStateMachineGraph')->willReturn('default');
        $requestConfiguration->expects($this->once())->method('getStateMachineTransition')->willReturn('transition');
        $this->stateMachine->expects($this->once())->method('apply')->with($resource, 'default', 'transition');

        $this->compositeStateMachine->apply($requestConfiguration, $resource);
    }
}
