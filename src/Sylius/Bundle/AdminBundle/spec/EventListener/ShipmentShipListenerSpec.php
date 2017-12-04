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

namespace spec\Sylius\Bundle\AdminBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AdminBundle\EmailManager\ShipmentEmailManagerInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ShipmentShipListenerSpec extends ObjectBehavior
{
    function let(ShipmentEmailManagerInterface $shipmentEmailManager): void
    {
        $this->beConstructedWith($shipmentEmailManager);
    }

    function it_sends_a_confirmation_email(
        ShipmentEmailManagerInterface $shipmentEmailManager,
        GenericEvent $event,
        ShipmentInterface $shipment
    ): void {
        $event->getSubject()->willReturn($shipment);

        $shipmentEmailManager->sendConfirmationEmail($shipment)->shouldBeCalled();

        $this->sendConfirmationEmail($event);
    }

    function it_throws_an_invalid_argument_exception_if_an_event_subject_is_not_a_shipment_instance(
        GenericEvent $event,
        \stdClass $shipment
    ): void {
        $event->getSubject()->willReturn($shipment);

        $this->shouldThrow(\InvalidArgumentException::class)->during('sendConfirmationEmail', [$event]);
    }
}
