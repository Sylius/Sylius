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

namespace Sylius\Bundle\CoreBundle\Tests\Functional\StateMachine;

use Sylius\Bundle\CoreBundle\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class OrderCheckoutWorkflowTest extends KernelTestCase
{
    /** @test */
    public function it_applies_address_transitions_for_order_checkout_cart_status(): void
    {
        $stateMachine = $this->getStateMachine();
        $subject = $this->createOrderWithCheckoutState();
        $stateMachine->apply($subject, 'sylius_order_checkout', 'address');

        $this->assertSame('addressed', $subject->getCheckoutState());
    }

    /**
     * @test
     *
     * @dataProvider availableTransitionsForAddressedStatus
     */
    public function it_applies_all_available_transitions_for_order_checkout_addressed_state(string $transition, string $expectedStatus): void
    {
        $stateMachine = $this->getStateMachine();
        $subject = $this->createOrderWithCheckoutState('addressed');

        $stateMachine->apply($subject, 'sylius_order_checkout', $transition);

        $this->assertSame($expectedStatus, $subject->getCheckoutState());
    }

    /**
     * @test
     *
     * @dataProvider availableTransitionsForShippingSelectedStatus
     */
    public function it_applies_all_available_transitions_for_order_checkout_shipping_selected_state(string $transition, string $expectedStatus): void
    {
        $stateMachine = $this->getStateMachine();
        $subject = $this->createOrderWithCheckoutState('shipping_selected');

        $stateMachine->apply($subject, 'sylius_order_checkout', $transition);

        $this->assertSame($expectedStatus, $subject->getCheckoutState());
    }

    /**
     * @test
     *
     * @dataProvider availableTransitionsForShippingSkippedStatus
     */
    public function it_applies_all_available_transitions_for_order_checkout_shipping_skipped_state(string $transition, string $expectedStatus): void
    {
        $stateMachine = $this->getStateMachine();
        $subject = $this->createOrderWithCheckoutState('shipping_skipped');

        $stateMachine->apply($subject, 'sylius_order_checkout', $transition);

        $this->assertSame($expectedStatus, $subject->getCheckoutState());
    }

    /**
     * @test
     *
     * @dataProvider availableTransitionsForPaymentSkippedStatus
     */
    public function it_applies_all_available_transitions_for_order_checkout_payment_skipped_state(string $transition, string $expectedStatus): void
    {
        $stateMachine = $this->getStateMachine();
        $subject = $this->createOrderWithCheckoutState('payment_skipped');

        $stateMachine->apply($subject, 'sylius_order_checkout', $transition);

        $this->assertSame($expectedStatus, $subject->getCheckoutState());
    }

    /**
     * @test
     *
     * @dataProvider availableTransitionsForPaymentSelectedStatus
     */
    public function it_applies_all_available_transitions_for_order_checkout_payment_selected_status(string $transition, string $expectedStatus): void
    {
        $stateMachine = $this->getStateMachine();
        $subject = $this->createOrderWithCheckoutState('payment_selected');
        $stateMachine->apply($subject, 'sylius_order_checkout', $transition);

        $this->assertSame($expectedStatus, $subject->getCheckoutState());
    }

    public function availableTransitionsForAddressedStatus(): iterable
    {
        yield ['address', 'addressed'];
        yield ['skip_shipping', 'shipping_skipped'];
        yield ['select_shipping', 'shipping_selected'];
    }

    public function availableTransitionsForShippingSelectedStatus(): iterable
    {
        yield ['address', 'addressed'];
        yield ['select_shipping', 'shipping_selected'];
        yield ['skip_payment', 'payment_skipped'];
        yield ['select_payment', 'payment_selected'];
    }

    public function availableTransitionsForShippingSkippedStatus(): iterable
    {
        yield ['address', 'addressed'];
        yield ['skip_payment', 'payment_skipped'];
        yield ['select_payment', 'payment_selected'];
    }

    public function availableTransitionsForPaymentSkippedStatus(): iterable
    {
        yield ['address', 'addressed'];
        yield ['select_shipping', 'shipping_selected'];
        yield ['complete', 'completed'];
    }

    public function availableTransitionsForPaymentSelectedStatus(): iterable
    {
        yield ['address', 'addressed'];
        yield ['select_shipping', 'shipping_selected'];
        yield ['select_payment', 'payment_selected'];
        yield ['complete', 'completed'];
    }

    private function createOrderWithCheckoutState(string $checkoutState = 'cart'): OrderInterface
    {
        $channel = $this->createMock(ChannelInterface::class);
        $order = new Order();
        $order->setChannel($channel);
        $order->setCheckoutState($checkoutState);

        return  $order;
    }

    private function getStateMachine(): StateMachineInterface
    {
        return self::getContainer()->get('sylius.state_machine.adapter.symfony_workflow');
    }
}
