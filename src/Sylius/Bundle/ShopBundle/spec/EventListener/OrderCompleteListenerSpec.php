<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShopBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ShopBundle\EmailManager\OrderEmailManagerInterface;
use Sylius\Bundle\ShopBundle\EventListener\OrderCompleteListener;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class OrderCompleteListenerSpec extends ObjectBehavior
{
    function let(OrderEmailManagerInterface $orderEmailManager)
    {
        $this->beConstructedWith($orderEmailManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderCompleteListener::class);
    }

    function it_sends_a_confirmation_email(
        OrderEmailManagerInterface $orderEmailManager,
        GenericEvent $event,
        OrderInterface $order
    ) {
        $event->getSubject()->willReturn($order);

        $orderEmailManager->sendConfirmationEmail($order)->shouldBeCalled();

        $this->sendConfirmationEmail($event);
    }

    function it_throws_an_invalid_argument_exception_if_an_event_subject_is_not_an_order_instance(
        GenericEvent $event,
        \stdClass $order
    ) {
        $event->getSubject()->willReturn($order);

        $this->shouldThrow(\InvalidArgumentException::class)->during('sendConfirmationEmail', [$event]);
    }
}
