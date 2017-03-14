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
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Bundle\UserBundle\EventListener\UserDeleteListener;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class UserDeleteListenerSpec extends ObjectBehavior
{
    function let(TokenStorageInterface $tokenStorage, SessionInterface $session, FlashBagInterface $flashBag)
    {
        $this->beConstructedWith($tokenStorage, $session);
        $session->getBag('flashes')->willReturn($flashBag);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UserDeleteListener::class);
    }

    function it_deletes_user_if_it_is_different_than_currently_logged_one(
        TokenStorageInterface $tokenStorage,
        FlashBagInterface $flashBag,
        ResourceControllerEvent $event,
        UserInterface $userToBeDeleted,
        UserInterface $currentlyLoggedUser,
        TokenInterface $tokenInterface
    ) {
        $event->getSubject()->willReturn($userToBeDeleted);
        $userToBeDeleted->getId()->willReturn(11);

        $tokenStorage->getToken()->willReturn($tokenInterface);
        $currentlyLoggedUser->getId()->willReturn(1);
        $tokenInterface->getUser()->willReturn($currentlyLoggedUser);

        $event->stopPropagation()->shouldNotBeCalled();
        $flashBag->add('error', Argument::any())->shouldNotBeCalled();

        $this->deleteUser($event);
    }

    function it_deletes_user_if_no_user_is_logged_in(
        TokenStorageInterface $tokenStorage,
        FlashBagInterface $flashBag,
        ResourceControllerEvent $event,
        UserInterface $userToBeDeleted,
        TokenInterface $tokenInterface
    ) {
        $event->getSubject()->willReturn($userToBeDeleted);
        $userToBeDeleted->getId()->willReturn(11);

        $tokenStorage->getToken()->willReturn($tokenInterface);
        $tokenInterface->getUser()->willReturn(null);

        $event->stopPropagation()->shouldNotBeCalled();
        $event->setErrorCode(Argument::any())->shouldNotBeCalled();
        $event->setMessage(Argument::any())->shouldNotBeCalled();
        $flashBag->add('error', Argument::any())->shouldNotBeCalled();

        $this->deleteUser($event);
    }

    function it_deletes_user_if_there_is_no_token(
        TokenStorageInterface $tokenStorage,
        FlashBagInterface $flashBag,
        ResourceControllerEvent $event,
        UserInterface $userToBeDeleted
    ) {
        $event->getSubject()->willReturn($userToBeDeleted);
        $userToBeDeleted->getId()->willReturn(11);

        $tokenStorage->getToken()->willReturn(null);

        $event->stopPropagation()->shouldNotBeCalled();
        $event->setErrorCode(Argument::any())->shouldNotBeCalled();
        $event->setMessage(Argument::any())->shouldNotBeCalled();
        $flashBag->add('error', Argument::any())->shouldNotBeCalled();

        $this->deleteUser($event);
    }

    function it_does_not_allow_to_delete_currently_logged_user(ResourceControllerEvent $event, UserInterface $userToBeDeleted, UserInterface $currentlyLoggedInUser, $tokenStorage, $flashBag, TokenInterface $token)
    {
        $event->getSubject()->willReturn($userToBeDeleted);
        $userToBeDeleted->getId()->willReturn(1);
        $tokenStorage->getToken()->willReturn($token);
        $currentlyLoggedInUser->getId()->willReturn(1);
        $token->getUser()->willReturn($currentlyLoggedInUser);

        $event->stopPropagation()->shouldBeCalled();
        $event->setErrorCode(Response::HTTP_UNPROCESSABLE_ENTITY)->shouldBeCalled();
        $event->setMessage('Cannot remove currently logged in user.')->shouldBeCalled();
        $flashBag->add('error', 'Cannot remove currently logged in user.')->shouldBeCalled();

        $this->deleteUser($event);
    }
}
