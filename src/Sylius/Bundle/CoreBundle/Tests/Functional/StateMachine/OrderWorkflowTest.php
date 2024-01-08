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

namespace Functional\StateMachine;

use Sylius\Component\Contracts\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class OrderWorkflowTest extends KernelTestCase
{
    private OrderInterface $order;

    public function setUp(): void
    {
        parent::setUp();

        $sequenceRepository = $this->createMock(RepositoryInterface::class);
        $sequenceRepository
            ->method('findOneBy')
            ->willReturn(null)
        ;

        self::getContainer()->set('sylius.repository.order_sequence', $sequenceRepository);

        $channel = $this->createMock(ChannelInterface::class);
        $customer = $this->createMock(Customer::class);

        $order = new Order();
        $order->setChannel($channel);
        $order->setCustomer($customer);

        $this->order = $order;
    }

    /**
     * @test
     *
     * @dataProvider availableTransitionsForOrder
     */
    public function it_applies_all_available_transitions_for_order(
        string $initialState,
        string $transition,
        string $expectedState,
    ): void {
        $stateMachine = $this->getStateMachine();
        $order = $this->order;

        $stateMachine->apply($order, OrderTransitions::GRAPH, $initialState);
        $stateMachine->apply($order, OrderTransitions::GRAPH, $transition);

        $this->assertSame($expectedState, $order->getState());
    }

    public function availableTransitionsForOrder(): iterable
    {
        yield [OrderTransitions::TRANSITION_CREATE, OrderTransitions::TRANSITION_CANCEL, OrderInterface::STATE_CANCELLED];
        yield [OrderTransitions::TRANSITION_CREATE, OrderTransitions::TRANSITION_FULFILL, OrderInterface::STATE_FULFILLED];
    }

    private function getStateMachine(): StateMachineInterface
    {
        return self::getContainer()->get('sylius.state_machine.adapter.symfony_workflow');
    }
}
