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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\MailerListener;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class MailerListenerTest extends TestCase
{
    private MockObject&SenderInterface $emailSender;

    private ChannelContextInterface&MockObject $channelContext;

    private LocaleContextInterface&MockObject $localeContext;

    private ChannelInterface&MockObject $channel;

    private MailerListener $mailerListener;

    protected function setUp(): void
    {
        $this->emailSender = $this->createMock(SenderInterface::class);
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->localeContext = $this->createMock(LocaleContextInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->mailerListener = new MailerListener(
            $this->emailSender,
            $this->channelContext,
            $this->localeContext,
        );
        $this->channelContext->method('getChannel')->willReturn($this->channel);
        $this->localeContext->method('getLocaleCode')->willReturn('en_US');
    }

    public function testThrowsExceptionIfEventSubjectIsNotCustomerInstanceSendingConfirmation(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $customer = new \stdClass();

        $event->method('getSubject')->willReturn($customer);
        $this->channel->method('isAccountVerificationRequired')->willReturn(false);

        $this->expectException(\InvalidArgumentException::class);

        $this->mailerListener->sendUserRegistrationEmail($event);
    }

    public function testDoesNotSendEmailIfCustomerUserIsNull(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $customer = $this->createMock(CustomerInterface::class);

        $event->method('getSubject')->willReturn($customer);
        $customer->method('getUser')->willReturn(null);
        $this->channel->method('isAccountVerificationRequired')->willReturn(false);

        $this->emailSender->expects($this->never())->method('send');

        $this->mailerListener->sendUserRegistrationEmail($event);
    }

    public function testDoesNotSendEmailIfCustomerUserHasNoEmail(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $customer = $this->createMock(CustomerInterface::class);
        $user = $this->createMock(ShopUserInterface::class);

        $event->method('getSubject')->willReturn($customer);
        $customer->method('getUser')->willReturn($user);
        $customer->method('getEmail')->willReturn(null);
        $this->channel->method('isAccountVerificationRequired')->willReturn(false);

        $this->emailSender->expects($this->never())->method('send');

        $this->mailerListener->sendUserRegistrationEmail($event);
    }

    public function testSendsEmailRegistrationSuccessfully(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $customer = $this->createMock(CustomerInterface::class);
        $user = $this->createMock(ShopUserInterface::class);

        $event->method('getSubject')->willReturn($customer);
        $customer->method('getUser')->willReturn($user);
        $customer->method('getEmail')->willReturn('fulanito@sylius.com');
        $user->method('getEmail')->willReturn('fulanito@sylius.com');
        $this->channel->method('isAccountVerificationRequired')->willReturn(false);

        $this->emailSender->expects($this->once())
            ->method('send')
            ->with(
                Emails::USER_REGISTRATION,
                ['fulanito@sylius.com'],
                [
                    'user' => $user,
                    'channel' => $this->channel,
                    'localeCode' => 'en_US',
                ],
            )
        ;

        $this->mailerListener->sendUserRegistrationEmail($event);
    }

    public function testDoesNothingWhenAccountVerificationIsRequired(): void
    {
        $event = $this->createMock(GenericEvent::class);

        $this->channel->method('isAccountVerificationRequired')->willReturn(true);

        $event->expects($this->never())->method('getSubject');

        $this->mailerListener->sendUserRegistrationEmail($event);
    }

    public function testSendPasswordResetTokenMail(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $user = $this->createMock(UserInterface::class);

        $event->method('getSubject')->willReturn($user);
        $user->method('getEmail')->willReturn('test@example.com');

        $this->emailSender->expects($this->once())
            ->method('send')
            ->with(
                'reset_password_token',
                ['test@example.com'],
                [
                    'user' => $user,
                    'channel' => $this->channel,
                    'localeCode' => 'en_US',
                ],
            )
        ;

        $this->mailerListener->sendResetPasswordTokenEmail($event);
    }

    public function testSendsVerificationSuccessEmail(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $shopUser = $this->createMock(ShopUserInterface::class);

        $event->method('getSubject')->willReturn($shopUser);
        $shopUser->method('getEmail')->willReturn('shop@example.com');

        $this->emailSender->expects($this->once())
            ->method('send')
            ->with(
                Emails::USER_REGISTRATION,
                ['shop@example.com'],
                [
                    'user' => $shopUser,
                    'channel' => $this->channel,
                    'localeCode' => 'en_US',
                ],
            )
        ;

        $this->mailerListener->sendVerificationSuccessEmail($event);
    }

    public function testThrowsExceptionWhileSendingVerificationSuccessEmailWhenEventSubjectIsNotShopUser(): void
    {
        $event = $this->createMock(GenericEvent::class);

        $event->method('getSubject')->willReturn(new \stdClass());

        $this->expectException(\InvalidArgumentException::class);

        $this->mailerListener->sendVerificationSuccessEmail($event);
    }
}
