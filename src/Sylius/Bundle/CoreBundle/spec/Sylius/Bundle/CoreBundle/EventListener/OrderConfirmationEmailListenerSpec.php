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

class OrderConfirmationEmailListenerSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\CoreBundle\Mailer\OrderConfirmationMailerInterface $mailer
     */
    function let($mailer)
    {
        $this->beConstructedWith($mailer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderConfirmationEmailListener');
    }

    /**
     * @param Symfony\Component\EventDispatcher\GenericEvent $event
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface  $order
     */
    function it_should_delegate_event_properly($event, $order, $mailer)
    {
        $event->getSubject()->willReturn($order);
        $mailer->sendOrderConfirmation($order)->shouldBeCalled();

        $this->processOrderConfirmation($event);
    }
}
