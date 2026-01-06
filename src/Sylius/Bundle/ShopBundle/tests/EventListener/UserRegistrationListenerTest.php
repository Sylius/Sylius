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

namespace Tests\Sylius\Bundle\ShopBundle\EventListener;

use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\EventListener\UserRegistrationListener;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class UserRegistrationListenerTest extends TestCase
{
    private MockObject&ObjectManager $userManager;

    private GeneratorInterface&MockObject $tokenGenerator;

    private EventDispatcherInterface&MockObject $eventDispatcher;

    private ChannelContextInterface&MockObject $channelContext;

    private MockObject&Security $security;

    private UserRegistrationListener $userRegistrationListener;

    protected function setUp(): void
    {
        $this->userManager = $this->createMock(ObjectManager::class);
        $this->tokenGenerator = $this->createMock(GeneratorInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->security = $this->createMock(Security::class);

        $this->userRegistrationListener = new UserRegistrationListener(
            $this->userManager,
            $this->tokenGenerator,
            $this->eventDispatcher,
            $this->channelContext,
            $this->security,
            'shop',
        );
    }

    public function testSendsAnUserVerificationEmail(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($customer);
        $customer->expects($this->once())->method('getUser')->willReturn($user);
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($channel);
        $channel->expects($this->once())->method('isAccountVerificationRequired')->willReturn(true);
        $this->tokenGenerator->expects($this->once())->method('generate')->willReturn('1d7dbc5c3dbebe5c');
        $user->expects($this->once())->method('setEmailVerificationToken')->with('1d7dbc5c3dbebe5c');
        $this->userManager->expects($this->once())->method('persist')->with($user);
        $this->userManager->expects($this->once())->method('flush');
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(GenericEvent::class), UserEvents::REQUEST_VERIFICATION_TOKEN)
        ;

        $this->userRegistrationListener->handleUserVerification($event);
    }

    public function testEnablesAndSignsInUser(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($customer);
        $customer->expects($this->once())->method('getUser')->willReturn($user);
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($channel);
        $channel->expects($this->once())->method('isAccountVerificationRequired')->willReturn(false);
        $user->expects($this->once())->method('setEnabled')->with(true);
        $this->userManager->expects($this->once())->method('persist')->with($user);
        $this->userManager->expects($this->once())->method('flush');
        $this->security->expects($this->once())->method('login')->with($user, 'form_login', 'shop');
        $this->tokenGenerator->expects($this->never())->method('generate');
        $user->expects($this->never())->method('setEmailVerificationToken');
        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch')
            ->with($this->isInstanceOf(GenericEvent::class), UserEvents::REQUEST_VERIFICATION_TOKEN)
        ;

        $this->userRegistrationListener->handleUserVerification($event);
    }

    public function testDoesNotSendVerificationEmailIfItIsNotRequiredOnChannel(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($customer);
        $customer->expects($this->once())->method('getUser')->willReturn($user);
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($channel);
        $channel->expects($this->once())->method('isAccountVerificationRequired')->willReturn(false);
        $user->expects($this->once())->method('setEnabled')->with(true);
        $this->userManager->expects($this->once())->method('persist')->with($user);
        $this->userManager->expects($this->once())->method('flush');
        $this->security->expects($this->once())->method('login')->with($user, 'form_login', 'shop');
        $this->tokenGenerator->expects($this->never())->method('generate');
        $user->expects($this->never())->method('setEmailVerificationToken');
        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch')
            ->with($this->isInstanceOf(GenericEvent::class), UserEvents::REQUEST_VERIFICATION_TOKEN)
        ;

        $this->userRegistrationListener->handleUserVerification($event);
    }

    public function testThrowsAnInvalidArgumentExceptionIfEventSubjectIsNotCustomerType(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        $event->expects($this->once())->method('getSubject')->willReturn(new \stdClass());

        $this->expectException(\InvalidArgumentException::class);

        $this->userRegistrationListener->handleUserVerification($event);
    }

    public function testThrowsAnInvalidArgumentExceptionIfUserIsNull(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($customer);
        $customer->expects($this->once())->method('getUser')->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        $this->userRegistrationListener->handleUserVerification($event);
    }
}
