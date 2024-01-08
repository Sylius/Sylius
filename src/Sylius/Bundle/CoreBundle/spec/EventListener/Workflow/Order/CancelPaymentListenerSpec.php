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

namespace spec\Sylius\Bundle\CoreBundle\EventListener\Workflow\Order;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Contracts\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class CancelPaymentListenerSpec extends ObjectBehavior
{
    function let(StateMachineInterface $compositeStateMachine): void
    {
        $this->beConstructedWith($compositeStateMachine);
    }

    function it_throws_an_exception_on_non_supported_subject(\stdClass $callback): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new CompletedEvent($callback->getWrappedObject(), new Marking())]);
    }

    function it_cancels_payments(
        StateMachineInterface $compositeStateMachine,
        OrderInterface $order,
        PaymentInterface $payment1,
        PaymentInterface $payment2,
    ): void {
        $event = new CompletedEvent($order->getWrappedObject(), new Marking());
        $order->getPayments()->willReturn(new ArrayCollection([$payment1->getWrappedObject(), $payment2->getWrappedObject()]));

        $this($event);

        $compositeStateMachine
            ->apply($payment1, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_CANCEL)
            ->shouldHaveBeenCalledOnce()
        ;

        $compositeStateMachine
            ->apply($payment2, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_CANCEL)
            ->shouldHaveBeenCalledOnce()
        ;
    }
}
