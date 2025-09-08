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

use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Bundle\UserBundle\EventListener\UserLastLoginSubscriber;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

final class UserLastLoginSubscriberTest extends TestCase
{
    private MockObject&ObjectManager $userManager;

    private UserLastLoginSubscriber $userLastLoginSubscriber;

    protected function setUp(): void
    {
        $this->userManager = $this->createMock(ObjectManager::class);

        $this->userLastLoginSubscriber = new UserLastLoginSubscriber(
            $this->userManager,
            UserInterface::class,
            null,
        );
    }

    public function testSubscriber(): void
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->userLastLoginSubscriber);
    }

    public function testItsSubscribedToEvents(): void
    {
        $this->assertSame(
            [
                SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
                UserEvents::SECURITY_IMPLICIT_LOGIN => 'onImplicitLogin',
            ],
            $this->userLastLoginSubscriber::getSubscribedEvents(),
        );
    }

    public function testUpdatesUserLastLoginOnSecurityInteractiveLogin(): void
    {
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);
        /** @var TokenInterface&MockObject $token */
        $token = $this->createMock(TokenInterface::class);
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $token->expects($this->once())->method('getUser')->willReturn($user);
        $user->expects($this->once())->method('setLastLogin')->with($this->isInstanceOf(\DateTimeInterface::class));
        $this->userManager->expects($this->once())->method('persist')->with($user);
        $this->userManager->expects($this->once())->method('flush');

        $this->userLastLoginSubscriber->onSecurityInteractiveLogin(new InteractiveLoginEvent($request, $token));
    }

    public function testUpdatesUserLastLoginOnImplicitLogin(): void
    {
        /** @var UserEvent&MockObject $event */
        $event = $this->createMock(UserEvent::class);
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $event->expects($this->once())->method('getUser')->willReturn($user);
        $user->expects($this->once())->method('setLastLogin')->with($this->isInstanceOf(\DateTimeInterface::class));
        $this->userManager->expects($this->once())->method('persist')->with($user);
        $this->userManager->expects($this->once())->method('flush');

        $this->userLastLoginSubscriber->onImplicitLogin($event);
    }

    public function testUpdatesOnlySyliusUserSpecifiedInConstructor(): void
    {
        /** @var UserEvent&MockObject $event */
        $event = $this->createMock(UserEvent::class);
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $this->userLastLoginSubscriber = new UserLastLoginSubscriber($this->userManager, 'FakeBundle\User\Model\User', null);

        $event->expects($this->once())->method('getUser')->willReturn($user);
        $user->expects($this->never())->method('setLastLogin');
        $this->userManager->expects($this->never())->method('persist');
        $this->userManager->expects($this->never())->method('flush');

        $this->userLastLoginSubscriber->onImplicitLogin($event);
    }

    public function testThrowsExceptionIfSubscriberIsUsedForClassOtherThanSyliusUserInterface(): void
    {
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);
        /** @var TokenInterface&MockObject $token */
        $token = $this->createMock(TokenInterface::class);
        /** @var SymfonyUserInterface&MockObject $user */
        $user = $this->createMock(SymfonyUserInterface::class);

        $this->userLastLoginSubscriber = new UserLastLoginSubscriber($this->userManager, SymfonyUserInterface::class, null);

        $token->expects($this->once())->method('getUser')->willReturn($user);
        $this->userManager->expects($this->never())->method('persist');
        $this->userManager->expects($this->never())->method('flush');

        $this->expectException(\UnexpectedValueException::class);

        $this->userLastLoginSubscriber->onSecurityInteractiveLogin(new InteractiveLoginEvent($request, $token));
    }

    public function testSetsLastLoginWhenThereWasNoneAndIntervalIsPresentOnInteractiveLogin(): void
    {
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);
        /** @var TokenInterface&MockObject $token */
        $token = $this->createMock(TokenInterface::class);
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $this->userLastLoginSubscriber = new UserLastLoginSubscriber($this->userManager, UserInterface::class, 'P1D');

        $token->expects($this->once())->method('getUser')->willReturn($user);
        $user->expects($this->once())->method('getLastLogin')->willReturn(null);
        $user->expects($this->once())->method('setLastLogin')->with($this->isInstanceOf(\DateTimeInterface::class));
        $this->userManager->expects($this->once())->method('persist')->with($user);
        $this->userManager->expects($this->once())->method('flush');

        $this->userLastLoginSubscriber->onSecurityInteractiveLogin(new InteractiveLoginEvent($request, $token));
    }

    public function testSetsLastLoginWhenThereWasNoneAndIntervalIsPresentOnImplicitLogin(): void
    {
        /** @var UserEvent&MockObject $event */
        $event = $this->createMock(UserEvent::class);
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $this->userLastLoginSubscriber = new UserLastLoginSubscriber($this->userManager, UserInterface::class, 'P1D');

        $user->expects($this->once())->method('getLastLogin')->willReturn(null);
        $event->expects($this->once())->method('getUser')->willReturn($user);
        $user->expects($this->once())->method('setLastLogin')->with($this->isInstanceOf(\DateTimeInterface::class));
        $this->userManager->expects($this->once())->method('persist')->with($user);
        $this->userManager->expects($this->once())->method('flush');

        $this->userLastLoginSubscriber->onImplicitLogin($event);
    }

    public function testDoesNothingWhenTrackingIntervalIsSetAndUserWasUpdatedWithinItOnInteractiveLogin(): void
    {
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);
        /** @var TokenInterface&MockObject $token */
        $token = $this->createMock(TokenInterface::class);
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $this->userLastLoginSubscriber = new UserLastLoginSubscriber($this->userManager, UserInterface::class, 'P1D');

        $token->expects($this->once())->method('getUser')->willReturn($user);
        $lastLogin = (new \DateTime())->modify('-6 hours');
        $user->expects($this->once())->method('getLastLogin')->willReturn($lastLogin);
        $user->expects($this->never())->method('setLastLogin');
        $this->userManager->expects($this->never())->method('persist')->with($user);
        $this->userManager->expects($this->never())->method('flush');
        $this->userLastLoginSubscriber->onSecurityInteractiveLogin(new InteractiveLoginEvent($request, $token));
    }

    public function testDoesNothingWhenTrackingIntervalIsSetAndUserWasUpdatedWithinItOnImplicitLogin(): void
    {
        /** @var UserEvent&MockObject $event */
        $event = $this->createMock(UserEvent::class);
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $this->userLastLoginSubscriber = new UserLastLoginSubscriber($this->userManager, UserInterface::class, 'P1D');

        $lastLogin = (new \DateTime())->modify('-6 hours');
        $user->expects($this->once())->method('getLastLogin')->willReturn($lastLogin);
        $event->expects($this->once())->method('getUser')->willReturn($user);
        $user->expects($this->never())->method('setLastLogin');
        $this->userManager->expects($this->never())->method('persist')->with($user);
        $this->userManager->expects($this->never())->method('flush');

        $this->userLastLoginSubscriber->onImplicitLogin($event);
    }

    public function testUpdatesLastLoginWhenThePreviousIsOlderThanTheIntervalOnInteractiveLogin(): void
    {
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);
        /** @var TokenInterface&MockObject $token */
        $token = $this->createMock(TokenInterface::class);
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $this->userLastLoginSubscriber = new UserLastLoginSubscriber($this->userManager, UserInterface::class, 'P1D');

        $token->expects($this->once())->method('getUser')->willReturn($user);
        $lastLogin = (new \DateTime())->modify('-3 days');
        $user->expects($this->once())->method('getLastLogin')->willReturn($lastLogin);
        $user->expects($this->once())->method('setLastLogin')->with($this->isInstanceOf(\DateTimeInterface::class));
        $this->userManager->expects($this->once())->method('persist')->with($user);
        $this->userManager->expects($this->once())->method('flush');

        $this->userLastLoginSubscriber->onSecurityInteractiveLogin(new InteractiveLoginEvent($request, $token));
    }

    public function testUpdatesLastLoginWhenThePreviousIsOlderThanTheIntervalOnImplicitLogin(): void
    {
        /** @var UserEvent&MockObject $event */
        $event = $this->createMock(UserEvent::class);
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $this->userLastLoginSubscriber = new UserLastLoginSubscriber($this->userManager, UserInterface::class, 'P1D');

        $lastLogin = (new \DateTime())->modify('-3 days');
        $user->expects($this->once())->method('getLastLogin')->willReturn($lastLogin);
        $event->expects($this->once())->method('getUser')->willReturn($user);
        $user->expects($this->once())->method('setLastLogin')->with($this->isInstanceOf(\DateTimeInterface::class));
        $this->userManager->expects($this->once())->method('persist')->with($user);
        $this->userManager->expects($this->once())->method('flush');

        $this->userLastLoginSubscriber->onImplicitLogin($event);
    }
}
