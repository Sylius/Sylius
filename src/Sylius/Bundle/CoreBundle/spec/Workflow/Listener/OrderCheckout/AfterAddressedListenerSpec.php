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

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Listener\OrderCheckout;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\AfterAddressedCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Listener\OrderCheckout\AfterAddressedListener;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;

final class AfterAddressedListenerSpec extends ObjectBehavior
{
    function let(
        AfterAddressedCallbackInterface $firstCallback,
        AfterAddressedCallbackInterface $secondCallback,
    ): void {
        $this->beConstructedWith([$firstCallback->getWrappedObject(), $secondCallback->getWrappedObject()]);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AfterAddressedListener::class);
    }

    function it_throws_an_exception_on_non_supported_callback(\stdClass $callback): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [[$callback->getWrappedObject()]]);
    }

    function it_calls_every_callbacks(
        Event $event,
        OrderInterface $order,
        AfterAddressedCallbackInterface $firstCallback,
        AfterAddressedCallbackInterface $secondCallback,
    ): void {
        $event->getSubject()->willReturn($order);

        $firstCallback->call($order)->shouldBeCalled();
        $secondCallback->call($order)->shouldBeCalled();

        $this->call($event);
    }
}
