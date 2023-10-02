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

use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Bundle\CoreBundle\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class OrderCheckoutWorkflowTest extends KernelTestCase
{
    /** @var OrderShippingMethodSelectionRequirementCheckerInterface|MockObject */
    private $orderShippingMethodSelectionRequirementChecker;

    /** @var OrderPaymentMethodSelectionRequirementCheckerInterface|MockObject */
    private $orderPaymentMethodSelectionRequirementChecker;

    public function setup(): void
    {
        parent::setUp();
        $promotionRepository = $this->createMock(PromotionRepositoryInterface::class);
        $promotionRepository
            ->method('findActiveNonCouponBasedByChannel')
            ->willReturn([])
        ;

        $this->orderShippingMethodSelectionRequirementChecker = $this->createMock(OrderShippingMethodSelectionRequirementCheckerInterface::class);
        $this->orderPaymentMethodSelectionRequirementChecker = $this->createMock(OrderPaymentMethodSelectionRequirementCheckerInterface::class);

        self::getContainer()->set('sylius.checker.order_shipping_method_selection_requirement', $this->orderShippingMethodSelectionRequirementChecker);
        self::getContainer()->set('sylius.checker.order_payment_method_selection_requirement', $this->orderPaymentMethodSelectionRequirementChecker);
        self::getContainer()->set('sylius.repository.promotion', $promotionRepository);
    }

    /**
     * @test
     *
     * @dataProvider availableTransitionsForCartStatus
     */
    public function it_applies_all_available_transitions_for_order_checkout_cart_status(
        string $transition,
        bool $isShippingMethodSelectionRequired,
        bool $isPaymentMethodSelectionRequired,
        string $expectedStatus,
    ): void {
        $this->setShippingMethodSelectionRequired($isShippingMethodSelectionRequired);
        $this->setPaymentMethodSelectionRequired($isPaymentMethodSelectionRequired);
        $stateMachine = $this->getStateMachine();
        $order = $this->createOrderWithCheckoutState();

        $stateMachine->apply($order, 'sylius_order_checkout', $transition);

        $this->assertSame($expectedStatus, $order->getCheckoutState());
    }

    /**
     * @test
     *
     * @dataProvider availableTransitionsForAddressedStatus
     */
    public function it_applies_all_available_transitions_for_order_checkout_addressed_state(string $transition, string $expectedStatus): void
    {
        $this->setShippingMethodSelectionRequired(true);
        $this->setPaymentMethodSelectionRequired(true);
        $stateMachine = $this->getStateMachine();
        $order = $this->createOrderWithCheckoutState('addressed');

        $stateMachine->apply($order, 'sylius_order_checkout', $transition);

        $this->assertSame($expectedStatus, $order->getCheckoutState());
    }

    /**
     * @test
     *
     * @dataProvider availableTransitionsForShippingSelectedStatus
     */
    public function it_applies_all_available_transitions_for_order_checkout_shipping_selected_state(string $transition, string $expectedStatus): void
    {
        $this->setShippingMethodSelectionRequired(true);
        $this->setPaymentMethodSelectionRequired(true);
        $stateMachine = $this->getStateMachine();
        $order = $this->createOrderWithCheckoutState('shipping_selected');

        $stateMachine->apply($order, 'sylius_order_checkout', $transition);

        $this->assertSame($expectedStatus, $order->getCheckoutState());
    }

    /**
     * @test
     *
     * @dataProvider availableTransitionsForShippingSkippedStatus
     */
    public function it_applies_all_available_transitions_for_order_checkout_shipping_skipped_state(string $transition, string $expectedStatus): void
    {
        $this->setShippingMethodSelectionRequired(true);
        $this->setPaymentMethodSelectionRequired(true);
        $stateMachine = $this->getStateMachine();
        $order = $this->createOrderWithCheckoutState('shipping_skipped');

        $stateMachine->apply($order, 'sylius_order_checkout', $transition);

        $this->assertSame($expectedStatus, $order->getCheckoutState());
    }

    /**
     * @test
     *
     * @dataProvider availableTransitionsForPaymentSkippedStatus
     */
    public function it_applies_all_available_transitions_for_order_checkout_payment_skipped_state(string $transition, string $expectedStatus): void
    {
        $this->setShippingMethodSelectionRequired(true);
        $this->setPaymentMethodSelectionRequired(true);
        $stateMachine = $this->getStateMachine();
        $order = $this->createOrderWithCheckoutState('payment_skipped');

        $stateMachine->apply($order, 'sylius_order_checkout', $transition);

        $this->assertSame($expectedStatus, $order->getCheckoutState());
    }

    /**
     * @test
     *
     * @dataProvider availableTransitionsForPaymentSelectedStatus
     */
    public function it_applies_all_available_transitions_for_order_checkout_payment_selected_status(string $transition, string $expectedStatus): void
    {
        $this->setShippingMethodSelectionRequired(true);
        $this->setPaymentMethodSelectionRequired(true);
        $stateMachine = $this->getStateMachine();
        $order = $this->createOrderWithCheckoutState('payment_selected');
        $stateMachine->apply($order, 'sylius_order_checkout', $transition);

        $this->assertSame($expectedStatus, $order->getCheckoutState());
    }

    public function availableTransitionsForCartStatus(): iterable
    {
        yield ['address', false, false, 'payment_skipped'];
        yield ['address', false, true, 'shipping_skipped'];
        yield ['address', true, false, 'addressed'];
        yield ['address', true, true, 'addressed'];
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
        $customer = $this->createMock(Customer::class);
        $order = new Order();
        $order->setChannel($channel);
        $order->setCustomer($customer);
        $order->setCheckoutState($checkoutState);
        $order->setShippingState(OrderShippingStates::STATE_READY);

        return  $order;
    }

    private function getStateMachine(): StateMachineInterface
    {
        return self::getContainer()->get('sylius.state_machine.adapter.symfony_workflow');
    }

    public function setShippingMethodSelectionRequired(bool $isShippingMethodSelectionRequired): void
    {
        $this->orderShippingMethodSelectionRequirementChecker
            ->method('isShippingMethodSelectionRequired')
            ->willReturn($isShippingMethodSelectionRequired);
    }

    private function setPaymentMethodSelectionRequired(bool $isPaymentMethodSelectionRequired): void
    {
        $this->orderPaymentMethodSelectionRequirementChecker
            ->method('isPaymentMethodSelectionRequired')
            ->willReturn($isPaymentMethodSelectionRequired);
    }
}
