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
use Sylius\Bundle\UserBundle\Security\UserLoginInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * User register listener spec.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserRegisterListenerSpec extends ObjectBehavior
{
    function let(PasswordUpdaterInterface $passwordUpdater, UserLoginInterface $userLogin)
    {
        $this->beConstructedWith($passwordUpdater, $userLogin);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\EventListener\UserRegisterListener');
    }

    function it_updates_user_password_before_registration($passwordUpdater, GenericEvent $event, UserInterface $user)
    {
        $event->getSubject()->willReturn($user);

        $passwordUpdater->updatePassword($user)->shouldBeCalled();

        $this->preRegistration($event);
    }

    function it_logs_user_in_after_registration($userLogin, GenericEvent $event, UserInterface $user)
    {
        $event->getSubject()->willReturn($user);

        $userLogin->login($user)->shouldBeCalled();

        $this->postRegistration($event);
    }

    function it_updates_password_only_for_user_interface_implementations(GenericEvent $event)
    {
        $user = '';
        $event->getSubject()->willReturn($user);
        $this->shouldThrow(new UnexpectedTypeException($user, 'Sylius\Component\User\Model\UserInterface'))
            ->duringPreRegistration($event);
    }

    function it_logs_only_user_interface_implementations_in(GenericEvent $event)
    {
        $user = '';
        $event->getSubject()->willReturn($user);
        $this->shouldThrow(new UnexpectedTypeException($user, 'Sylius\Component\User\Model\UserInterface'))
            ->duringPostRegistration($event);
    }
}
