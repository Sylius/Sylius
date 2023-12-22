<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\EventListener\Workflow\Shipment;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ShippingBundle\Assigner\ShippingDateAssignerInterface;
use Sylius\Component\Core\Model\Shipment;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\Marking;

final class AssignShippingDateListenerSpec extends ObjectBehavior
{
    function let(ShippingDateAssignerInterface $shippingDateAssigner): void
    {
        $this->beConstructedWith($shippingDateAssigner);
    }

    function it_throws_an_exception_on_non_supported_subject(\stdClass $subject): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new TransitionEvent($subject->getWrappedObject(), new Marking())])
        ;
    }

    function it_resolves_order_state_after_order_being_shipped(
        ShippingDateAssignerInterface $shippingDateAssigner,
    ): void {
        $shipment = new Shipment();
        $event = new TransitionEvent($shipment, new Marking());

        $shippingDateAssigner->assign($shipment)->shouldBeCalled();

        $this->__invoke($event);
    }
}
