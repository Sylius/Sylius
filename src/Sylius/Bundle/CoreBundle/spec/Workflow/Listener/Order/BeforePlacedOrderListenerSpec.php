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

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Listener\Order;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\BeforePlacedOrderCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Listener\Order\BeforePlacedOrderListener;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;

final class BeforePlacedOrderListenerSpec extends ObjectBehavior
{
    function let(
        BeforePlacedOrderCallbackInterface $firstCallback,
        BeforePlacedOrderCallbackInterface $secondCallback,
    ): void {
        $this->beConstructedWith([$firstCallback->getWrappedObject(), $secondCallback->getWrappedObject()]);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(BeforePlacedOrderListener::class);
    }

    function it_calls_every_callbacks(
        Event $event,
        OrderInterface $order,
        BeforePlacedOrderCallbackInterface $firstCallback,
        BeforePlacedOrderCallbackInterface $secondCallback,
    ): void {
        $event->getSubject()->willReturn($order);

        $firstCallback->call($order)->shouldBeCalled();
        $secondCallback->call($order)->shouldBeCalled();

        $this->call($event);
    }
}
