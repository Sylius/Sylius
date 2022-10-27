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
use Sylius\Bundle\CoreBundle\Workflow\Callback\Payment\AfterProcessedPaymentCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Listener\Payment\AfterProcessedPaymentListener;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Workflow\Event\Event;

final class AfterProcessedPaymentListenerSpec extends ObjectBehavior
{
    function let(
        AfterProcessedPaymentCallbackInterface $firstCallback,
        AfterProcessedPaymentCallbackInterface $secondCallback,
    ): void {
        $this->beConstructedWith([$firstCallback->getWrappedObject(), $secondCallback->getWrappedObject()]);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AfterProcessedPaymentListener::class);
    }

    function it_throws_an_exception_on_non_supported_callback(\stdClass $callback): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [[$callback->getWrappedObject()]]);
    }

    function it_calls_every_callbacks(
        Event $event,
        PaymentInterface $payment,
        AfterProcessedPaymentCallbackInterface $firstCallback,
        AfterProcessedPaymentCallbackInterface $secondCallback,
    ): void {
        $event->getSubject()->willReturn($payment);

        $firstCallback->call($payment)->shouldBeCalled();
        $secondCallback->call($payment)->shouldBeCalled();

        $this->call($event);
    }
}
