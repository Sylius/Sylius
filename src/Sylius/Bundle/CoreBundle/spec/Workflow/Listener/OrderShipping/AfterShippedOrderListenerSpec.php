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

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Listener\OrderShipping;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderShipping\AfterShippedOrderCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Listener\OrderShipping\AfterShippedOrderListener;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;

final class AfterShippedOrderListenerSpec extends ObjectBehavior
{
    function let(
        AfterShippedOrderCallbackInterface $firstCallback,
        AfterShippedOrderCallbackInterface $secondCallback,
    ): void {
        $this->beConstructedWith([$firstCallback->getWrappedObject(), $secondCallback->getWrappedObject()]);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AfterShippedOrderListener::class);
    }

    function it_calls_every_callbacks(
        Event $event,
        OrderInterface $order,
        AfterShippedOrderCallbackInterface $firstCallback,
        AfterShippedOrderCallbackInterface $secondCallback,
    ): void {
        $event->getSubject()->willReturn($order);

        $firstCallback->call($order)->shouldBeCalled();
        $secondCallback->call($order)->shouldBeCalled();

        $this->call($event);
    }
}
