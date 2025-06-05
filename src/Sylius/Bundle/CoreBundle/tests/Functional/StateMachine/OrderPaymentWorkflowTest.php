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

namespace Tests\Sylius\Bundle\CoreBundle\Functional\StateMachine;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\Order;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class OrderPaymentWorkflowTest extends KernelTestCase
{
    #[Test]
    public function it_applies_available_transition_for_order_payment_cart_status(): void
    {
        $stateMachine = $this->getStateMachine();
        $order = new Order();

        $stateMachine->apply($order, 'sylius_order_payment', 'request_payment');

        $this->assertSame('awaiting_payment', $order->getPaymentState());
    }

    #[DataProvider('availableTransitionsForAwaitingPaymentState')]
    #[Test]
    public function it_applies_all_available_transitions_for_order_payment_awaiting_payment_state(
        string $transition,
        string $expectedStatus,
    ): void {
        $stateMachine = $this->getStateMachine();
        $order = new Order();
        $order->setPaymentState('awaiting_payment');
        $stateMachine->apply($order, 'sylius_order_payment', $transition);

        $this->assertSame($expectedStatus, $order->getPaymentState());
    }

    #[DataProvider('availableTransitionsForPartiallyAuthorizedState')]
    #[Test]
    public function it_applies_all_available_transitions_for_order_payment_partially_authorized_state(
        string $transition,
        string $expectedStatus,
    ): void {
        $stateMachine = $this->getStateMachine();
        $order = new Order();
        $order->setPaymentState('partially_authorized');
        $stateMachine->apply($order, 'sylius_order_payment', $transition);

        $this->assertSame($expectedStatus, $order->getPaymentState());
    }

    #[DataProvider('availableTransitionsForAuthorizedState')]
    #[Test]
    public function it_applies_all_available_transitions_for_order_payment_authorized_state(
        string $transition,
        string $expectedStatus,
    ): void {
        $stateMachine = $this->getStateMachine();
        $order = new Order();
        $order->setPaymentState('authorized');
        $stateMachine->apply($order, 'sylius_order_payment', $transition);

        $this->assertSame($expectedStatus, $order->getPaymentState());
    }

    #[DataProvider('availableTransitionsForPartiallyPaidState')]
    #[Test]
    public function it_applies_all_available_transitions_for_order_payment_partially_paid_state(
        string $transition,
        string $expectedStatus,
    ): void {
        $stateMachine = $this->getStateMachine();
        $order = new Order();
        $order->setPaymentState('partially_paid');
        $stateMachine->apply($order, 'sylius_order_payment', $transition);

        $this->assertSame($expectedStatus, $order->getPaymentState());
    }

    #[DataProvider('availableTransitionsForPaidState')]
    #[Test]
    public function it_applies_all_available_transitions_for_order_payment_paid_state(
        string $transition,
        string $expectedStatus,
    ): void {
        $stateMachine = $this->getStateMachine();
        $order = new Order();
        $order->setPaymentState('paid');
        $stateMachine->apply($order, 'sylius_order_payment', $transition);

        $this->assertSame($expectedStatus, $order->getPaymentState());
    }

    #[DataProvider('availableTransitionsForPartiallyRefundedState')]
    #[Test]
    public function it_applies_all_available_transitions_for_order_partially_refunded_state(
        string $transition,
        string $expectedStatus,
    ): void {
        $stateMachine = $this->getStateMachine();
        $order = new Order();
        $order->setPaymentState('partially_refunded');
        $stateMachine->apply($order, 'sylius_order_payment', $transition);

        $this->assertSame($expectedStatus, $order->getPaymentState());
    }

    public static function availableTransitionsForAwaitingPaymentState(): iterable
    {
        yield ['partially_authorize', 'partially_authorized'];
        yield ['authorize', 'authorized'];
        yield ['partially_pay', 'partially_paid'];
        yield ['cancel', 'cancelled'];
        yield ['pay', 'paid'];
    }

    public static function availableTransitionsForPartiallyAuthorizedState(): iterable
    {
        yield ['partially_authorize', 'partially_authorized'];
        yield ['authorize', 'authorized'];
        yield ['partially_pay', 'partially_paid'];
        yield ['cancel', 'cancelled'];
    }

    public static function availableTransitionsForAuthorizedState(): iterable
    {
        yield ['cancel', 'cancelled'];
        yield ['pay', 'paid'];
    }

    public static function availableTransitionsForPartiallyPaidState(): iterable
    {
        yield ['partially_pay', 'partially_paid'];
        yield ['pay', 'paid'];
        yield ['partially_refund', 'partially_refunded'];
        yield ['refund', 'refunded'];
    }

    public static function availableTransitionsForPaidState(): iterable
    {
        yield ['partially_refund', 'partially_refunded'];
        yield ['refund', 'refunded'];
    }

    public static function availableTransitionsForPartiallyRefundedState(): iterable
    {
        yield ['partially_refund', 'partially_refunded'];
        yield ['refund', 'refunded'];
    }

    private function getStateMachine(): StateMachineInterface
    {
        return self::getContainer()->get('sylius_abstraction.state_machine.adapter.symfony_workflow');
    }
}
