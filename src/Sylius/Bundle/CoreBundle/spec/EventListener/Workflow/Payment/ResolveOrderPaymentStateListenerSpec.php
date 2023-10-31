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

namespace spec\Sylius\Bundle\CoreBundle\EventListener\Workflow\Payment;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class ResolveOrderPaymentStateListenerSpec extends ObjectBehavior
{
    function let(StateResolverInterface $stateResolver): void
    {
        $this->beConstructedWith($stateResolver);
    }

    function it_throws_exception_when_event_subject_is_not_a_payment(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new CompletedEvent(new \stdClass(), new Marking())])
        ;
    }

    function it_throws_exception_when_event_payment_has_no_order(PaymentInterface $payment): void
    {
        $payment->getOrder()->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new CompletedEvent($payment->getWrappedObject(), new Marking())])
        ;
    }

    function it_resolves_order_payment_state(
        StateResolverInterface $stateResolver,
        PaymentInterface $payment,
        OrderInterface $order,
    ): void {
        $payment->getOrder()->willReturn($order);

        $stateResolver->resolve($order)->shouldBeCalled();

        $this->__invoke(new CompletedEvent($payment->getWrappedObject(), new Marking()));
    }
}
