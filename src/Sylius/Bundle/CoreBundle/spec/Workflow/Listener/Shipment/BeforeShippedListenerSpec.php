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
use Sylius\Bundle\CoreBundle\Workflow\Callback\Shipment\BeforeShippedCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Listener\Shipment\BeforeShippedListener;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\Workflow\Event\Event;

final class BeforeShippedListenerSpec extends ObjectBehavior
{
    function let(
        BeforeShippedCallbackInterface $firstCallback,
        BeforeShippedCallbackInterface $secondCallback,
    ): void {
        $this->beConstructedWith([$firstCallback->getWrappedObject(), $secondCallback->getWrappedObject()]);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(BeforeShippedListener::class);
    }

    function it_calls_every_callbacks(
        Event $event,
        ShipmentInterface $shipment,
        BeforeShippedCallbackInterface $firstCallback,
        BeforeShippedCallbackInterface $secondCallback,
    ): void {
        $event->getSubject()->willReturn($shipment);

        $firstCallback->call($shipment)->shouldBeCalled();
        $secondCallback->call($shipment)->shouldBeCalled();

        $this->call($event);
    }
}
