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
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserLoginListenerSpec extends ObjectBehavior
{
    function let(UserLoginInterface $loginMenager)
    {
        $this->beConstructedWith($loginMenager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\EventListener\UserLoginListener');
    }

    function it_logs_user_in($loginMenager, GenericEvent $event, UserInterface $user)
    {
        $event->getSubject()->willReturn($user);

        $loginMenager->login($user)->shouldBeCalled();

        $this->login($event);
    }

    function it_logs_in_user_implementation_only($loginMenager, GenericEvent $event, UserInterface $user)
    {
        $user = '';
        $event->getSubject()->willReturn($user);
        $this->shouldThrow(new UnexpectedTypeException($user, 'Sylius\Component\User\Model\UserInterface'))
            ->duringLogin($event);
    }
}
