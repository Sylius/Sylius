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
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Payment\ProcessOrderListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Payment\ResolveOrderPaymentStateListener;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class PaymentWorkflowTest extends KernelTestCase
{
    /** @var PaymentInterface|MockObject */
    protected PaymentInterface $payment;

    /** @var OrderInterface|MockObject */
    protected OrderInterface $order;

    public function setUp(): void
    {
        parent::setUp();
        $this->order = new Order();

        $this->payment = new Payment();
        $this->payment->setOrder($this->order);
    }

    #[DataProvider('availableTransitions')]
    #[Test]
    public function it_applies_all_available_transitions(
        string $fromState,
        string $transition,
        string $toState,
    ): void {
        $this->payment->setState($fromState);

        $stateMachine = $this->getStateMachine();
        $stateMachine->apply($this->payment, PaymentTransitions::GRAPH, $transition);

        $this->assertSame($toState, $this->payment->getState());
    }

    public static function availableTransitions(): iterable
    {
        yield [PaymentInterface::STATE_CART, PaymentTransitions::TRANSITION_CREATE, PaymentInterface::STATE_NEW];
        yield [PaymentInterface::STATE_NEW, PaymentTransitions::TRANSITION_PROCESS, PaymentInterface::STATE_PROCESSING];
        yield [PaymentInterface::STATE_NEW, PaymentTransitions::TRANSITION_AUTHORIZE, PaymentInterface::STATE_AUTHORIZED];
        yield [PaymentInterface::STATE_PROCESSING, PaymentTransitions::TRANSITION_AUTHORIZE, PaymentInterface::STATE_AUTHORIZED];
        yield [PaymentInterface::STATE_NEW, PaymentTransitions::TRANSITION_COMPLETE, PaymentInterface::STATE_COMPLETED];
        yield [PaymentInterface::STATE_PROCESSING, PaymentTransitions::TRANSITION_COMPLETE, PaymentInterface::STATE_COMPLETED];
        yield [PaymentInterface::STATE_AUTHORIZED, PaymentTransitions::TRANSITION_COMPLETE, PaymentInterface::STATE_COMPLETED];
        yield [PaymentInterface::STATE_NEW, PaymentTransitions::TRANSITION_FAIL, PaymentInterface::STATE_FAILED];
        yield [PaymentInterface::STATE_PROCESSING, PaymentTransitions::TRANSITION_FAIL, PaymentInterface::STATE_FAILED];
        yield [PaymentInterface::STATE_AUTHORIZED, PaymentTransitions::TRANSITION_FAIL, PaymentInterface::STATE_FAILED];
        yield [PaymentInterface::STATE_NEW, PaymentTransitions::TRANSITION_CANCEL, PaymentInterface::STATE_CANCELLED];
        yield [PaymentInterface::STATE_PROCESSING, PaymentTransitions::TRANSITION_CANCEL, PaymentInterface::STATE_CANCELLED];
        yield [PaymentInterface::STATE_AUTHORIZED, PaymentTransitions::TRANSITION_CANCEL, PaymentInterface::STATE_CANCELLED];
        yield [PaymentInterface::STATE_COMPLETED, PaymentTransitions::TRANSITION_REFUND, PaymentInterface::STATE_REFUNDED];
    }

    #[Test]
    public function it_fires_resolve_order_payment_state_listener_after_process_order_listener_on_cancel(): void
    {
        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = self::getContainer()->get('event_dispatcher');

        $cancelListeners = $dispatcher->getListeners('workflow.sylius_payment.completed.cancel');

        $listenerClasses = array_map(
            static fn (mixed $listener): string => is_array($listener) ? get_class($listener[0]) : get_class($listener),
            $cancelListeners,
        );

        $processOrderIndex = array_search(ProcessOrderListener::class, $listenerClasses, true);
        $resolveStateIndex = array_search(ResolveOrderPaymentStateListener::class, $listenerClasses, true);

        $this->assertNotFalse($processOrderIndex, 'ProcessOrderListener must be subscribed to completed.cancel');
        $this->assertNotFalse($resolveStateIndex, 'ResolveOrderPaymentStateListener must be subscribed to completed.cancel');
        $this->assertGreaterThan($processOrderIndex, $resolveStateIndex, 'ResolveOrderPaymentStateListener must run after ProcessOrderListener (higher index = lower priority)');
    }

    #[Test]
    public function it_fires_resolve_order_payment_state_listener_after_process_order_listener_on_fail(): void
    {
        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = self::getContainer()->get('event_dispatcher');

        $failListeners = $dispatcher->getListeners('workflow.sylius_payment.completed.fail');

        $listenerClasses = array_map(
            static fn (mixed $listener): string => is_array($listener) ? get_class($listener[0]) : get_class($listener),
            $failListeners,
        );

        $processOrderIndex = array_search(ProcessOrderListener::class, $listenerClasses, true);
        $resolveStateIndex = array_search(ResolveOrderPaymentStateListener::class, $listenerClasses, true);

        $this->assertNotFalse($processOrderIndex, 'ProcessOrderListener must be subscribed to completed.fail');
        $this->assertNotFalse($resolveStateIndex, 'ResolveOrderPaymentStateListener must be subscribed to completed.fail');
        $this->assertGreaterThan($processOrderIndex, $resolveStateIndex, 'ResolveOrderPaymentStateListener must run after ProcessOrderListener (higher index = lower priority)');
    }

    private function getStateMachine(): StateMachineInterface
    {
        return self::getContainer()->get('sylius_abstraction.state_machine.adapter.symfony_workflow');
    }
}
