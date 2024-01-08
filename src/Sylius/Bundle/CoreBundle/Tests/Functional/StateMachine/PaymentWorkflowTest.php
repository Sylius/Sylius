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
use Sylius\Component\Contracts\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

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

    /**
     * @test
     *
     * @dataProvider availableTransitions
     */
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

    public function availableTransitions(): iterable
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

    private function getStateMachine(): StateMachineInterface
    {
        return self::getContainer()->get('sylius.state_machine.adapter.symfony_workflow');
    }
}
