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

use Sylius\Bundle\CoreBundle\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class OrderWorkflowTest extends KernelTestCase
{
    private OrderInterface $order;

    public function setup(): void
    {
        parent::setUp();

        $channel = $this->createMock(ChannelInterface::class);
        $customer = $this->createMock(Customer::class);
        $order = new Order();
        $order->setChannel($channel);
        $order->setCustomer($customer);

        $this->order = $order;
    }

    /** @test */
    public function it_applies_new_state_for_order_cart_state(): void
    {
        $stateMachine = $this->getStateMachine();
        $order = $this->order;

        $this->assertSame('cart', $order->getState());

        $stateMachine->apply($order, 'sylius_order', 'create');

        $this->assertSame('new', $order->getState());
    }

    /**
     * @test
     *
     * @dataProvider availableTransitionsForNewState
     */
    public function it_applies_all_available_transitions_for_order_new_state(
        string $transition,
        string $expectedState,
    ): void {
        $stateMachine = $this->getStateMachine();
        $order = $this->order;

        $stateMachine->apply($order, 'sylius_order', 'create');
        $stateMachine->apply($order, 'sylius_order', $transition);

        $this->assertSame($expectedState, $order->getState());
    }

    public function availableTransitionsForNewState(): iterable
    {
        yield ['cancel', 'cancelled'];
        yield ['fulfill', 'fulfilled'];
    }

    private function getStateMachine(): StateMachineInterface
    {
        return self::getContainer()->get('sylius.state_machine.adapter.symfony_workflow');
    }
}
