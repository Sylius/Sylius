<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Symfony\Component\EventDispatcher\GenericEvent;

use Sylius\Bundle\CoreBundle\Mailer\OrderConfirmationMailerInterface;
use Sylius\Component\Core\Model\OrderInterface;

class OrderConfirmationEmailListenerSpec extends ObjectBehavior
{
    public function let(OrderConfirmationMailerInterface $mailer)
    {
        $this->beConstructedWith($mailer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderConfirmationEmailListener');
    }

    public function it_should_delegate_event_properly(GenericEvent $event, OrderInterface $order, $mailer)
    {
        $event->getSubject()->willReturn($order);
        $mailer->sendOrderConfirmation($order)->shouldBeCalled();

        $this->processOrderConfirmation($event);
    }
}
