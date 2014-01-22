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

use FOS\UserBundle\Event\FilterUserResponseEvent;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Mailer\CustomerWelcomeMailerInterface;
use Sylius\Bundle\CoreBundle\Model\UserInterface;

class CustomerWelcomeEmailListenerSpec extends ObjectBehavior
{
    function let(CustomerWelcomeMailerInterface $mailer)
    {
        $this->beConstructedWith($mailer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\CustomerWelcomeEmailListener');
    }

    function it_should_delegate_event_properly(FilterUserResponseEvent $event, UserInterface $user, $mailer)
    {
        $user->isEnabled()->willReturn(true);
        $event->getUser()->willReturn($user);
        $mailer->sendCustomerWelcome($user)->shouldBeCalled();

        $this->handleEvent($event);
    }

    function it_should_not_email_disabled_users(FilterUserResponseEvent $event, UserInterface $user, $mailer)
    {
        $user->isEnabled()->willReturn(false);
        $event->getUser()->willReturn($user);
        $mailer->sendCustomerWelcome($user)->shouldNotBeCalled();

        $this->handleEvent($event);
    }
}
