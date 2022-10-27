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

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Listener\OrderPayment;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderPayment\AfterPaidCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Listener\OrderPayment\AfterPaidListener;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;

final class AfterPaidListenerSpec extends ObjectBehavior
{
    function let(
        AfterPaidCallbackInterface $firstCallback,
        AfterPaidCallbackInterface $secondCallback,
    ): void {
        $this->beConstructedWith([$firstCallback->getWrappedObject(), $secondCallback->getWrappedObject()]);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AfterPaidListener::class);
    }

    function it_throws_an_exception_on_non_supported_callback(\stdClass $callback): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [[$callback->getWrappedObject()]]);
    }

    function it_calls_every_callbacks(
        Event $event,
        OrderInterface $order,
        AfterPaidCallbackInterface $firstCallback,
        AfterPaidCallbackInterface $secondCallback,
    ): void {
        $event->getSubject()->willReturn($order);

        $firstCallback->call($order)->shouldBeCalled();
        $secondCallback->call($order)->shouldBeCalled();

        $this->call($event);
    }
}
