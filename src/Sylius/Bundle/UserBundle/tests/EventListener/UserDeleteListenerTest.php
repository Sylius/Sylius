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

namespace Tests\Sylius\Bundle\UserBundle\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UserBundle\EventListener\UserDeleteListener;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class UserDeleteListenerTest extends TestCase
{
    private MockObject&TokenStorageInterface $tokenStorage;

    private MockObject&RequestStack $requestStack;

    private MockObject&SessionInterface $session;

    private FlashBagInterface&MockObject $flashBag;

    private UserDeleteListener $userDeleteListener;

    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->session = $this->createMock(SessionInterface::class);
        $this->flashBag = $this->createMock(FlashBagInterface::class);

        $this->userDeleteListener = new UserDeleteListener($this->tokenStorage, $this->requestStack);
    }

    public function testDeletesUserIfItIsDifferentThanCurrentlyLoggedOne(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var UserInterface&MockObject $userToBeDeleted */
        $userToBeDeleted = $this->createMock(UserInterface::class);
        /** @var UserInterface&MockObject $currentlyLoggedUser */
        $currentlyLoggedUser = $this->createMock(UserInterface::class);
        /** @var TokenInterface&MockObject $tokenInterface */
        $tokenInterface = $this->createMock(TokenInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($userToBeDeleted);
        $userToBeDeleted->expects($this->once())->method('getId')->willReturn(11);
        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn($tokenInterface);
        $currentlyLoggedUser->expects($this->once())->method('getId')->willReturn(1);
        $tokenInterface->expects($this->once())->method('getUser')->willReturn($currentlyLoggedUser);
        $event->expects($this->never())->method('stopPropagation');
        $this->flashBag->expects($this->never())->method('add');

        $this->userDeleteListener->deleteUser($event);
    }

    public function testDeletesUserIfNoUserIsLoggedIn(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var UserInterface&MockObject $userToBeDeleted */
        $userToBeDeleted = $this->createMock(UserInterface::class);
        /** @var TokenInterface&MockObject $tokenInterface */
        $tokenInterface = $this->createMock(TokenInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($userToBeDeleted);
        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn($tokenInterface);
        $tokenInterface->expects($this->once())->method('getUser')->willReturn(null);
        $event->expects($this->never())->method('stopPropagation');
        $event->expects($this->never())->method('setErrorCode');
        $event->expects($this->never())->method('setMessage');
        $this->flashBag->expects($this->never())->method('add');

        $this->userDeleteListener->deleteUser($event);
    }

    public function testDeletesUserIfThereIsNoToken(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var UserInterface&MockObject $userToBeDeleted */
        $userToBeDeleted = $this->createMock(UserInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($userToBeDeleted);
        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn(null);
        $event->expects($this->never())->method('stopPropagation');
        $event->expects($this->never())->method('setErrorCode');
        $event->expects($this->never())->method('setMessage');
        $this->flashBag->expects($this->never())->method('add');

        $this->userDeleteListener->deleteUser($event);
    }

    public function testDoesNotAllowToDeleteCurrentlyLoggedUser(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var UserInterface&MockObject $userToBeDeleted */
        $userToBeDeleted = $this->createMock(UserInterface::class);
        /** @var UserInterface&MockObject $currentlyLoggedInUser */
        $currentlyLoggedInUser = $this->createMock(UserInterface::class);
        /** @var TokenInterface&MockObject $token */
        $token = $this->createMock(TokenInterface::class);

        $this->requestStack->expects($this->once())->method('getSession')->willReturn($this->session);
        $this->session->expects($this->once())->method('getBag')->with('flashes')->willReturn($this->flashBag);

        $event->expects($this->once())->method('getSubject')->willReturn($userToBeDeleted);
        $userToBeDeleted->expects($this->once())->method('getId')->willReturn(1);
        $userToBeDeleted->expects($this->once())->method('getRoles')->willReturn(['ROLE_ADMINISTRATION_ACCESS']);

        $currentlyLoggedInUser->expects($this->once())->method('getId')->willReturn(1);
        $currentlyLoggedInUser->expects($this->once())->method('getRoles')->willReturn(['ROLE_ADMINISTRATION_ACCESS']);

        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn($token);

        $token->expects($this->once())->method('getUser')->willReturn($currentlyLoggedInUser);
        $event->expects($this->once())->method('stopPropagation');
        $event->expects($this->once())->method('setErrorCode')->with(Response::HTTP_UNPROCESSABLE_ENTITY);
        $event->expects($this->once())->method('setMessage')->with('Cannot remove currently logged in user.');

        $this->flashBag->expects($this->once())->method('add')->with('error', 'Cannot remove currently logged in user.');

        $this->userDeleteListener->deleteUser($event);
    }

    public function testDeletesShopUserEvenIfAdminUserHasSameId(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var UserInterface&MockObject $userToBeDeleted */
        $userToBeDeleted = $this->createMock(UserInterface::class);
        /** @var UserInterface&MockObject $currentlyLoggedInUser */
        $currentlyLoggedInUser = $this->createMock(UserInterface::class);
        /** @var TokenInterface&MockObject $token */
        $token = $this->createMock(TokenInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($userToBeDeleted);
        $userToBeDeleted->expects($this->once())->method('getId')->willReturn(1);
        $userToBeDeleted->expects($this->once())->method('getRoles')->willReturn(['ROLE_API_ACCESS']);

        $currentlyLoggedInUser->expects($this->once())->method('getId')->willReturn(1);
        $currentlyLoggedInUser->expects($this->once())->method('getRoles')->willReturn(['ROLE_ADMINISTRATION_ACCESS']);

        $this->tokenStorage->expects($this->once())->method('getToken')->willReturn($token);
        $token->expects($this->once())->method('getUser')->willReturn($currentlyLoggedInUser);
        $event->expects($this->never())->method('stopPropagation');
        $event->expects($this->never())->method('setErrorCode');
        $event->expects($this->never())->method('setMessage');

        $this->flashBag->expects($this->never())->method('add');

        $this->userDeleteListener->deleteUser($event);
    }
}
