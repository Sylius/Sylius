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
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * User delete listener spec.
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class UserDeleteListenerSpec extends ObjectBehavior
{
    function let(TokenStorageInterface $tokenStorage, SessionInterface $session, FlashBagInterface $flashBag)
    {
        $this->beConstructedWith($tokenStorage, $session);
        $session->getBag('flashes')->willReturn($flashBag);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\EventListener\UserDeleteListener');
    }

    function it_deletes_user_if_it_is_different_than_currently_loggged_one(GenericEvent $event, UserInterface $userToBeDeleted, UserInterface $currentlyLoggedUser, $flashBag, $tokenStorage, TokenInterface $tokenInterface)
    {
        $event->getSubject()->willReturn($userToBeDeleted);
        $userToBeDeleted->getId()->willReturn(11);

        $tokenStorage->getToken()->willReturn($tokenInterface);
        $currentlyLoggedUser->getId()->willReturn(1);
        $tokenInterface->getUser()->willReturn($currentlyLoggedUser);

        $event->stopPropagation()->shouldNotBeCalled();
        $flashBag->add('error', Argument::any())->shouldNotBeCalled();
        $this->deleteUser($event);
    }

    function it_deletes_user_if_no_user_is_logged_in(GenericEvent $event, UserInterface $userToBeDeleted, $flashBag, $tokenStorage, TokenInterface $tokenInterface)
    {
        $event->getSubject()->willReturn($userToBeDeleted);
        $userToBeDeleted->getId()->willReturn(11);

        $tokenStorage->getToken()->willReturn($tokenInterface);
        $tokenInterface->getUser()->willReturn(null);

        $event->stopPropagation()->shouldNotBeCalled();
        $flashBag->add('error', Argument::any())->shouldNotBeCalled();
        $this->deleteUser($event);
    }

    function it_deletes_user_if_there_is_no_token(GenericEvent $event, UserInterface $userToBeDeleted, $flashBag, $tokenStorage)
    {
        $event->getSubject()->willReturn($userToBeDeleted);
        $userToBeDeleted->getId()->willReturn(11);

        $tokenStorage->getToken()->willReturn(null);

        $event->stopPropagation()->shouldNotBeCalled();
        $flashBag->add('error', Argument::any())->shouldNotBeCalled();
        $this->deleteUser($event);
    }

    function it_does_not_allow_to_delete_currently_logged_user(GenericEvent $event, UserInterface $userToBeDeleted, UserInterface $currentlyLoggedInUser, $tokenStorage, $flashBag, TokenInterface $token)
    {
        $event->getSubject()->willReturn($userToBeDeleted);
        $userToBeDeleted->getId()->willReturn(1);
        $tokenStorage->getToken()->willReturn($token);
        $currentlyLoggedInUser->getId()->willReturn(1);
        $token->getUser()->willReturn($currentlyLoggedInUser);

        $event->stopPropagation()->shouldBeCalled();
        $flashBag->add('error', 'Cannot remove currently logged in user.')->shouldBeCalled();
        $this->deleteUser($event);
    }
}
