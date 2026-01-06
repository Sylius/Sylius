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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\ShopBundle\EventListener\CustomerEmailUpdaterListener;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopSection;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CustomerEmailUpdaterListenerTest extends TestCase
{
    private GeneratorInterface&MockObject $tokenGenerator;

    private ChannelContextInterface&MockObject $channelContext;

    private EventDispatcherInterface&MockObject $eventDispatcher;

    private MockObject&RequestStack $requestStack;

    private MockObject&SectionProviderInterface $sectionResolver;

    private MockObject&TokenStorageInterface $tokenStorage;

    private CustomerEmailUpdaterListener $customerEmailUpdaterListener;

    protected function setUp(): void
    {
        $this->tokenGenerator = $this->createMock(GeneratorInterface::class);
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->sectionResolver = $this->createMock(SectionProviderInterface::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);

        $this->customerEmailUpdaterListener = new CustomerEmailUpdaterListener(
            $this->tokenGenerator,
            $this->channelContext,
            $this->eventDispatcher,
            $this->requestStack,
            $this->sectionResolver,
            $this->tokenStorage,
        );
    }

    public function testDoesNothingChangeWasPerformedByAdminDuringEraseVerification(): void
    {
        /** @var SectionInterface&MockObject $section */
        $section = $this->createMock(SectionInterface::class);
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($section);
        $event->expects($this->never())->method('getSubject');

        $this->customerEmailUpdaterListener->eraseVerification($event);
    }

    public function testDoesNothingChangeWasPerformedByAdminDuringSendVerificationEmail(): void
    {
        /** @var SectionInterface&MockObject $section */
        $section = $this->createMock(SectionInterface::class);
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($section);
        $event->expects($this->never())->method('getSubject');

        $this->customerEmailUpdaterListener->sendVerificationEmail($event);
    }

    public function testRemovesUserVerificationAndDisablesUserIfEmailHasBeenChangedAndChannelRequiresVerification(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($shopSection);
        $event->expects($this->once())->method('getSubject')->willReturn($customer);
        $customer->expects($this->once())->method('getUser')->willReturn($user);
        $customer->expects($this->once())->method('getEmail')->willReturn('new@example.com');

        $this->tokenGenerator->expects($this->once())->method('generate')->willReturn('1d7dbc5c3dbebe5c');
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($channel);
        $channel->expects($this->once())->method('isAccountVerificationRequired')->willReturn(true);

        $user->expects($this->once())->method('getUsername')->willReturn('old@example.com');
        $user->expects($this->once())->method('setVerifiedAt')->with(null);
        $user->expects($this->once())->method('setEmailVerificationToken')->with('1d7dbc5c3dbebe5c');
        $user->expects($this->once())->method('setEnabled')->with(false);

        $this->tokenStorage->expects($this->once())->method('setToken')->with(null);

        $this->customerEmailUpdaterListener->eraseVerification($event);
    }

    public function testRemovesUserVerificationOnlyIfEmailHasBeenChangedButChannelDoesNotRequireVerification(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($shopSection);
        $event->expects($this->once())->method('getSubject')->willReturn($customer);
        $customer->expects($this->once())->method('getUser')->willReturn($user);
        $customer->expects($this->once())->method('getEmail')->willReturn('new@example.com');

        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($channel);
        $channel->expects($this->once())->method('isAccountVerificationRequired')->willReturn(false);

        $user->expects($this->once())->method('getUsername')->willReturn('old@example.com');
        $user->expects($this->once())->method('setVerifiedAt')->with(null);
        $user->expects($this->never())->method('setEmailVerificationToken');
        $user->expects($this->never())->method('setEnabled')->with(false);

        $this->tokenGenerator->expects($this->never())->method('generate');
        $this->tokenStorage->expects($this->never())->method('setToken')->with(null);

        $this->customerEmailUpdaterListener->eraseVerification($event);
    }

    public function testDoesNothingIfEmailHasNotBeenChanged(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($shopSection);
        $event->expects($this->once())->method('getSubject')->willReturn($customer);
        $customer->expects($this->once())->method('getUser')->willReturn($user);
        $customer->expects($this->once())->method('getEmail')->willReturn('new@example.com');

        $this->channelContext->expects($this->never())->method('getChannel');

        $user->expects($this->once())->method('getUsername')->willReturn('new@example.com');
        $user->expects($this->never())->method('setVerifiedAt')->with(null);
        $user->expects($this->never())->method('setEmailVerificationToken');
        $user->expects($this->never())->method('setEnabled')->with(false);

        $this->tokenGenerator->expects($this->never())->method('generate');
        $this->tokenStorage->expects($this->never())->method('setToken')->with(null);

        $this->customerEmailUpdaterListener->eraseVerification($event);
    }

    public function testThrowsAnInvalidArgumentExceptionIfEventSubjectIsNotCustomerType(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($shopSection);
        $event->expects($this->once())->method('getSubject')->willReturn(new \stdClass());

        $this->expectException(\InvalidArgumentException::class);

        $this->customerEmailUpdaterListener->sendVerificationEmail($event);
    }

    public function testThrowsAnInvalidArgumentExceptionIfUserIsNull(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($shopSection);
        $event->expects($this->once())->method('getSubject')->willReturn($customer);
        $customer->expects($this->once())->method('getUser')->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        $this->customerEmailUpdaterListener->sendVerificationEmail($event);
    }

    public function testSendsVerificationEmailAndAddsFlashIfUserVerificationIsRequired(): void
    {
        /** @var SessionInterface&MockObject $session */
        $session = $this->createMock(SessionInterface::class);
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var FlashBagInterface&MockObject $flashBag */
        $flashBag = $this->createMock(FlashBagInterface::class);
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($shopSection);
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($channel);
        $channel->expects($this->once())->method('isAccountVerificationRequired')->willReturn(true);
        $event->expects($this->once())->method('getSubject')->willReturn($customer);
        $customer->expects($this->once())->method('getUser')->willReturn($user);
        $user->expects($this->once())->method('isEnabled')->willReturn(false);
        $user->expects($this->once())->method('isVerified')->willReturn(false);
        $user->expects($this->once())->method('getEmailVerificationToken')->willReturn('1d7dbc5c3dbebe5c');
        $this->requestStack->expects($this->once())->method('getSession')->willReturn($session);
        $session->expects($this->once())->method('getBag')->with('flashes')->willReturn($flashBag);
        $flashBag->expects($this->once())->method('add')->with('success', 'sylius.user.verify_email_request');
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(GenericEvent::class), UserEvents::REQUEST_VERIFICATION_TOKEN)
        ;

        $this->customerEmailUpdaterListener->sendVerificationEmail($event);
    }

    public function testDoesNotSendEmailIfUserIsStillEnabled(): void
    {
        /** @var SessionInterface&MockObject $session */
        $session = $this->createMock(SessionInterface::class);
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var FlashBagInterface&MockObject $flashBag */
        $flashBag = $this->createMock(FlashBagInterface::class);
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($shopSection);
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($channel);
        $channel->expects($this->once())->method('isAccountVerificationRequired')->willReturn(true);
        $event->expects($this->once())->method('getSubject')->willReturn($customer);
        $customer->expects($this->once())->method('getUser')->willReturn($user);
        $user->expects($this->once())->method('isEnabled')->willReturn(true);
        $user->expects($this->never())->method('isVerified');
        $user->expects($this->never())->method('getEmailVerificationToken');
        $session->expects($this->never())->method('getBag')->with('flashes');
        $flashBag->expects($this->never())->method('add');
        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch')
            ->with($this->isInstanceOf(GenericEvent::class), UserEvents::REQUEST_VERIFICATION_TOKEN)
        ;

        $this->customerEmailUpdaterListener->sendVerificationEmail($event);
    }

    public function testDoesNotSendEmailIfUserIsStillVerified(): void
    {
        /** @var SessionInterface&MockObject $session */
        $session = $this->createMock(SessionInterface::class);
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var FlashBagInterface&MockObject $flashBag */
        $flashBag = $this->createMock(FlashBagInterface::class);
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($shopSection);
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($channel);
        $channel->expects($this->once())->method('isAccountVerificationRequired')->willReturn(true);
        $event->expects($this->once())->method('getSubject')->willReturn($customer);
        $customer->expects($this->once())->method('getUser')->willReturn($user);
        $user->expects($this->once())->method('isEnabled')->willReturn(false);
        $user->expects($this->once())->method('isVerified')->willReturn(true);
        $user->expects($this->never())->method('getEmailVerificationToken');
        $session->expects($this->never())->method('getBag')->with('flashes');
        $flashBag->expects($this->never())->method('add');
        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch')
            ->with($this->isInstanceOf(GenericEvent::class), UserEvents::REQUEST_VERIFICATION_TOKEN)
        ;

        $this->customerEmailUpdaterListener->sendVerificationEmail($event);
    }

    public function testDoesNotSendEmailIfUserDoesNotHaveVerificationToken(): void
    {
        /** @var SessionInterface&MockObject $session */
        $session = $this->createMock(SessionInterface::class);
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var FlashBagInterface&MockObject $flashBag */
        $flashBag = $this->createMock(FlashBagInterface::class);
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($shopSection);
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($channel);
        $channel->expects($this->once())->method('isAccountVerificationRequired')->willReturn(true);
        $event->expects($this->once())->method('getSubject')->willReturn($customer);
        $customer->expects($this->once())->method('getUser')->willReturn($user);
        $user->expects($this->once())->method('isEnabled')->willReturn(false);
        $user->expects($this->once())->method('isVerified')->willReturn(false);
        $user->expects($this->once())->method('getEmailVerificationToken')->willReturn(null);
        $session->expects($this->never())->method('getBag')->with('flashes');
        $flashBag->expects($this->never())->method('add');
        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch')
            ->with($this->isInstanceOf(GenericEvent::class), UserEvents::REQUEST_VERIFICATION_TOKEN)
        ;

        $this->customerEmailUpdaterListener->sendVerificationEmail($event);
    }

    public function testDoesNothingIfChannelDoesNotRequireVerification(): void
    {
        /** @var SessionInterface&MockObject $session */
        $session = $this->createMock(SessionInterface::class);
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var FlashBagInterface&MockObject $flashBag */
        $flashBag = $this->createMock(FlashBagInterface::class);
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($shopSection);
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($channel);
        $channel->expects($this->once())->method('isAccountVerificationRequired')->willReturn(false);
        $event->expects($this->once())->method('getSubject')->willReturn($customer);
        $customer->expects($this->once())->method('getUser')->willReturn($user);
        $session->expects($this->never())->method('getBag')->with('flashes');
        $flashBag->expects($this->never())->method('add');
        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch')
            ->with($this->isInstanceOf(GenericEvent::class), UserEvents::REQUEST_VERIFICATION_TOKEN)
        ;

        $this->customerEmailUpdaterListener->sendVerificationEmail($event);
    }
}
