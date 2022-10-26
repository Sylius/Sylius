<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Listener\Payment;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Payment\AfterCanceledPaymentCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Listener\Payment\AfterCanceledPaymentListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Workflow\Event\Event;

final class AfterCanceledPaymentListenerSpec extends ObjectBehavior
{
    function let(
        AfterCanceledPaymentCallbackInterface $firstCallback,
        AfterCanceledPaymentCallbackInterface $secondCallback,
    ): void {
        $this->beConstructedWith([$firstCallback->getWrappedObject(), $secondCallback->getWrappedObject()]);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AfterCanceledPaymentListener::class);
    }

    function it_calls_every_callbacks(
        Event $event,
        PaymentInterface $payment,
        AfterCanceledPaymentCallbackInterface $firstCallback,
        AfterCanceledPaymentCallbackInterface $secondCallback,
    ): void {
        $event->getSubject()->willReturn($payment);

        $firstCallback->call($payment)->shouldBeCalled();
        $secondCallback->call($payment)->shouldBeCalled();

        $this->call($event);
    }
}
