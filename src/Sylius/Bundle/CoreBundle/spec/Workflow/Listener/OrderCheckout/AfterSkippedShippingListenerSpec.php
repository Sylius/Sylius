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
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\AfterSkippedShippingCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Listener\OrderCheckout\AfterSkippedShippingListener;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;

final class AfterSkippedShippingListenerSpec extends ObjectBehavior
{
    function let(
        AfterSkippedShippingCallbackInterface $firstCallback,
        AfterSkippedShippingCallbackInterface $secondCallback,
    ): void {
        $this->beConstructedWith([$firstCallback->getWrappedObject(), $secondCallback->getWrappedObject()]);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AfterSkippedShippingListener::class);
    }

    function it_calls_every_callbacks(
        Event $event,
        OrderInterface $order,
        AfterSkippedShippingCallbackInterface $firstCallback,
        AfterSkippedShippingCallbackInterface $secondCallback,
    ): void {
        $event->getSubject()->willReturn($order);

        $firstCallback->call($order)->shouldBeCalled();
        $secondCallback->call($order)->shouldBeCalled();

        $this->call($event);
    }
}
