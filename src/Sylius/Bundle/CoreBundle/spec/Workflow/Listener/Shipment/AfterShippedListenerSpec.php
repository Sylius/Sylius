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

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Listener\Shipment;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Shipment\AfterShippedCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Listener\Shipment\AfterShippedListener;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\Workflow\Event\Event;

final class AfterShippedListenerSpec extends ObjectBehavior
{
    function let(
        AfterShippedCallbackInterface $firstCallback,
        AfterShippedCallbackInterface $secondCallback,
    ): void {
        $this->beConstructedWith([$firstCallback->getWrappedObject(), $secondCallback->getWrappedObject()]);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AfterShippedListener::class);
    }

    function it_throws_an_exception_on_non_supported_callback(\stdClass $callback): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [[$callback->getWrappedObject()]]);
    }

    function it_calls_every_callbacks(
        Event $event,
        ShipmentInterface $shipment,
        AfterShippedCallbackInterface $firstCallback,
        AfterShippedCallbackInterface $secondCallback,
    ): void {
        $event->getSubject()->willReturn($shipment);

        $firstCallback->call($shipment)->shouldBeCalled();
        $secondCallback->call($shipment)->shouldBeCalled();

        $this->call($event);
    }
}
