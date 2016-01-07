<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\UserBundle\Security\UserLoginInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class UserAutoLoginListenerSpec extends ObjectBehavior
{
    function let(UserLoginInterface $loginManager)
    {
        $this->beConstructedWith($loginManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\EventListener\UserAutoLoginListener');
    }

    function it_logs_user_in($loginManager, GenericEvent $event, CustomerInterface $customer, UserInterface $user)
    {
        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);

        $loginManager->login($user)->shouldBeCalled();

        $this->login($event);
    }

    function it_does_not_log_user_in_if_customer_does_not_have_assigned_user($loginManager, GenericEvent $event, CustomerInterface $customer)
    {
        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn(null);

        $loginManager->login(Argument::any())->shouldNotBeCalled();

        $this->login($event);
    }

    function it_logs_in_user_implementation_only(GenericEvent $event)
    {
        $customer = '';
        $event->getSubject()->willReturn($customer);
        $this->shouldThrow(new UnexpectedTypeException($customer, CustomerInterface::class))
            ->duringLogin($event);
    }
}
