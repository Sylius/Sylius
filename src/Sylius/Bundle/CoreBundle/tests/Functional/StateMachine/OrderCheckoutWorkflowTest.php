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
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
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

        $sequenceRepository = $this->createMock(RepositoryInterface::class);
        $sequenceRepository
            ->method('findOneBy')
            ->willReturn(null)
        ;

        $this->orderShippingMethodSelectionRequirementChecker = $this->createMock(OrderShippingMethodSelectionRequirementCheckerInterface::class);
        $this->orderPaymentMethodSelectionRequirementChecker = $this->createMock(OrderPaymentMethodSelectionRequirementCheckerInterface::class);

        self::getContainer()->set('sylius.checker.order_shipping_method_selection_requirement', $this->orderShippingMethodSelectionRequirementChecker);
        self::getContainer()->set('sylius.checker.order_payment_method_selection_requirement', $this->orderPaymentMethodSelectionRequirementChecker);
        self::getContainer()->set('sylius.repository.promotion', $promotionRepository);
        self::getContainer()->set('sylius.repository.order_sequence', $sequenceRepository);
    }

    #[DataProvider('availableTransitionsForCartState')]
    #[Test]
    public function it_applies_all_available_transitions_for_order_checkout_cart_state(
        string $transition,
        bool $isShippingMethodSelectionRequired,
        bool $isPaymentMethodSelectionRequired,
        string $expectedState,
    ): void {
        $this->setShippingMethodSelectionRequired($isShippingMethodSelectionRequired);
        $this->setPaymentMethodSelectionRequired($isPaymentMethodSelectionRequired);
        $stateMachine = $this->getStateMachine();
        $order = $this->createOrderWithCheckoutState();

        $stateMachine->apply($order, 'sylius_order_checkout', $transition);

        $this->assertSame($expectedState, $order->getCheckoutState());
    }

    #[DataProvider('availableTransitionsForAddressedState')]
    #[Test]
    public function it_applies_all_available_transitions_for_order_checkout_addressed_state(string $transition, string $expectedState): void
    {
        $this->setShippingMethodSelectionRequired(true);
        $this->setPaymentMethodSelectionRequired(true);
        $stateMachine = $this->getStateMachine();
        $order = $this->createOrderWithCheckoutState('addressed');

        $stateMachine->apply($order, 'sylius_order_checkout', $transition);

        $this->assertSame($expectedState, $order->getCheckoutState());
    }

    #[DataProvider('availableTransitionsForShippingSelectedState')]
    #[Test]
    public function it_applies_all_available_transitions_for_order_checkout_shipping_selected_state(string $transition, string $expectedState): void
    {
        $this->setShippingMethodSelectionRequired(true);
        $this->setPaymentMethodSelectionRequired(true);
        $stateMachine = $this->getStateMachine();
        $order = $this->createOrderWithCheckoutState('shipping_selected');

        $stateMachine->apply($order, 'sylius_order_checkout', $transition);

        $this->assertSame($expectedState, $order->getCheckoutState());
    }

    #[DataProvider('availableTransitionsForShippingSkippedState')]
    #[Test]
    public function it_applies_all_available_transitions_for_order_checkout_shipping_skipped_state(string $transition, string $expectedState): void
    {
        $this->setShippingMethodSelectionRequired(true);
        $this->setPaymentMethodSelectionRequired(true);
        $stateMachine = $this->getStateMachine();
        $order = $this->createOrderWithCheckoutState('shipping_skipped');

        $stateMachine->apply($order, 'sylius_order_checkout', $transition);

        $this->assertSame($expectedState, $order->getCheckoutState());
    }

    #[DataProvider('availableTransitionsForPaymentSkippedState')]
    #[Test]
    public function it_applies_all_available_transitions_for_order_checkout_payment_skipped_state(string $transition, string $expectedState): void
    {
        $this->setShippingMethodSelectionRequired(true);
        $this->setPaymentMethodSelectionRequired(true);
        $stateMachine = $this->getStateMachine();
        $order = $this->createOrderWithCheckoutState('payment_skipped');

        $stateMachine->apply($order, 'sylius_order_checkout', $transition);

        $this->assertSame($expectedState, $order->getCheckoutState());
    }

    #[DataProvider('availableTransitionsForPaymentSelectedState')]
    #[Test]
    public function it_applies_all_available_transitions_for_order_checkout_payment_selected_state(string $transition, string $expectedState): void
    {
        $this->setShippingMethodSelectionRequired(true);
        $this->setPaymentMethodSelectionRequired(true);
        $stateMachine = $this->getStateMachine();
        $order = $this->createOrderWithCheckoutState('payment_selected');
        $stateMachine->apply($order, 'sylius_order_checkout', $transition);

        $this->assertSame($expectedState, $order->getCheckoutState());
    }

    public static function availableTransitionsForCartState(): iterable
    {
        yield ['address', false, false, 'payment_skipped'];
        yield ['address', false, true, 'shipping_skipped'];
        yield ['address', true, false, 'addressed'];
        yield ['address', true, true, 'addressed'];
    }

    public static function availableTransitionsForAddressedState(): iterable
    {
        yield ['address', 'addressed'];
        yield ['skip_shipping', 'shipping_skipped'];
        yield ['select_shipping', 'shipping_selected'];
    }

    public static function availableTransitionsForShippingSelectedState(): iterable
    {
        yield ['address', 'addressed'];
        yield ['select_shipping', 'shipping_selected'];
        yield ['skip_payment', 'payment_skipped'];
        yield ['select_payment', 'payment_selected'];
    }

    public static function availableTransitionsForShippingSkippedState(): iterable
    {
        yield ['address', 'addressed'];
        yield ['skip_payment', 'payment_skipped'];
        yield ['select_payment', 'payment_selected'];
    }

    public static function availableTransitionsForPaymentSkippedState(): iterable
    {
        yield ['address', 'addressed'];
        yield ['select_shipping', 'shipping_selected'];
        yield ['complete', 'completed'];
    }

    public static function availableTransitionsForPaymentSelectedState(): iterable
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

        return  $order;
    }

    private function getStateMachine(): StateMachineInterface
    {
        return self::getContainer()->get('sylius_abstraction.state_machine.adapter.symfony_workflow');
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
