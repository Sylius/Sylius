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

namespace Tests\Sylius\Bundle\CoreBundle\CommandHandler\Account;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Command\Account\ResendVerificationEmail;
use Sylius\Bundle\CoreBundle\CommandHandler\Account\ResendVerificationEmailHandler;
use Sylius\Bundle\CoreBundle\Mailer\Emails as CoreEmails;
use Sylius\Bundle\UserBundle\Mailer\Emails as UserEmails;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[CoversClass(ResendVerificationEmailHandler::class)]
final class ResendVerificationEmailHandlerTest extends TestCase
{
    /** @var MockObject&UserRepositoryInterface<ShopUserInterface> */
    private MockObject&UserRepositoryInterface $shopUserRepository;

    /** @var ChannelRepositoryInterface<ChannelInterface>&MockObject */
    private ChannelRepositoryInterface&MockObject $channelRepository;

    private GeneratorInterface&MockObject $tokenGenerator;

    private SenderInterface&MockObject $emailSender;

    private ObjectManager&MockObject $shopUserManager;

    private ResendVerificationEmailHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shopUserRepository = $this->createMock(UserRepositoryInterface::class);
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->tokenGenerator = $this->createMock(GeneratorInterface::class);
        $this->emailSender = $this->createMock(SenderInterface::class);
        $this->shopUserManager = $this->createMock(ObjectManager::class);
        $this->handler = new ResendVerificationEmailHandler(
            $this->shopUserRepository,
            $this->channelRepository,
            $this->tokenGenerator,
            $this->emailSender,
            $this->shopUserManager,
        );
    }

    public function testIsAMessageHandler(): void
    {
        $attributes = (new \ReflectionClass($this->handler::class))->getAttributes(AsMessageHandler::class);

        self::assertCount(1, $attributes);
    }

    public function testSendsTokenEmail(): void
    {
        /** @var MockObject&ShopUserInterface $shopUser */
        $shopUser = $this->createMock(ShopUserInterface::class);
        $shopUser->method('isVerified')->willReturn(false);
        $shopUser->method('getEmail')->willReturn('test@example.com');

        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('isAccountVerificationRequired')->willReturn(true);

        $this->shopUserRepository->expects(self::once())->method('findOneByEmail')->with('test@example.com')->willReturn($shopUser);
        $this->channelRepository->expects(self::once())->method('findOneByCode')->with('WEB')->willReturn($channel);
        $this->tokenGenerator->expects(self::once())->method('generate')->willReturn('TOKEN');
        $shopUser->expects(self::once())->method('setEmailVerificationToken')->with('TOKEN');
        $this->shopUserManager->expects(self::once())->method('flush');
        $this->emailSender->expects(self::once())
            ->method('send')
            ->with(
                CoreEmails::ACCOUNT_VERIFICATION,
                ['test@example.com'],
                ['user' => $shopUser, 'channel' => $channel, 'localeCode' => 'en_US'],
            );

        ($this->handler)(new ResendVerificationEmail(channelCode: 'WEB', localeCode: 'en_US', email: 'test@example.com'));
    }

    public function testSendsLinkEmail(): void
    {
        /** @var MockObject&ShopUserInterface $shopUser */
        $shopUser = $this->createMock(ShopUserInterface::class);
        $shopUser->method('isVerified')->willReturn(false);
        $shopUser->method('getEmail')->willReturn('test@example.com');

        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('isAccountVerificationRequired')->willReturn(true);

        $this->shopUserRepository->expects(self::once())->method('findOneByEmail')->with('test@example.com')->willReturn($shopUser);
        $this->channelRepository->expects(self::once())->method('findOneByCode')->with('WEB')->willReturn($channel);
        $this->tokenGenerator->expects(self::once())->method('generate')->willReturn('TOKEN');
        $shopUser->expects(self::once())->method('setEmailVerificationToken')->with('TOKEN');
        $this->shopUserManager->expects(self::once())->method('flush');
        $this->emailSender->expects(self::once())
            ->method('send')
            ->with(
                UserEmails::EMAIL_VERIFICATION_TOKEN,
                ['test@example.com'],
                ['user' => $shopUser, 'channel' => $channel, 'localeCode' => 'en_US'],
            );

        ($this->handler)(new ResendVerificationEmail(channelCode: 'WEB', localeCode: 'en_US', email: 'test@example.com', sendVerificationLink: true));
    }

    public function testDoesNothingWhenUserNotFound(): void
    {
        $this->shopUserRepository->expects(self::once())->method('findOneByEmail')->with('unknown@example.com')->willReturn(null);
        $this->channelRepository->expects(self::never())->method('findOneByCode');
        $this->tokenGenerator->expects(self::never())->method('generate');
        $this->shopUserManager->expects(self::never())->method('flush');
        $this->emailSender->expects(self::never())->method('send');

        ($this->handler)(new ResendVerificationEmail(channelCode: 'WEB', localeCode: 'en_US', email: 'unknown@example.com'));
    }

    public function testDoesNothingWhenUserIsAlreadyVerified(): void
    {
        /** @var MockObject&ShopUserInterface $shopUser */
        $shopUser = $this->createMock(ShopUserInterface::class);
        $shopUser->method('isVerified')->willReturn(true);

        $this->shopUserRepository->expects(self::once())->method('findOneByEmail')->with('verified@example.com')->willReturn($shopUser);
        $this->channelRepository->expects(self::never())->method('findOneByCode');
        $this->tokenGenerator->expects(self::never())->method('generate');
        $this->shopUserManager->expects(self::never())->method('flush');
        $this->emailSender->expects(self::never())->method('send');

        ($this->handler)(new ResendVerificationEmail(channelCode: 'WEB', localeCode: 'en_US', email: 'verified@example.com'));
    }

    public function testDoesNothingWhenChannelHasVerificationDisabled(): void
    {
        /** @var MockObject&ShopUserInterface $shopUser */
        $shopUser = $this->createMock(ShopUserInterface::class);
        $shopUser->method('isVerified')->willReturn(false);

        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('isAccountVerificationRequired')->willReturn(false);

        $this->shopUserRepository->expects(self::once())->method('findOneByEmail')->with('test@example.com')->willReturn($shopUser);
        $this->channelRepository->expects(self::once())->method('findOneByCode')->with('WEB')->willReturn($channel);
        $this->tokenGenerator->expects(self::never())->method('generate');
        $this->shopUserManager->expects(self::never())->method('flush');
        $this->emailSender->expects(self::never())->method('send');

        ($this->handler)(new ResendVerificationEmail(channelCode: 'WEB', localeCode: 'en_US', email: 'test@example.com'));
    }

    public function testDoesNothingWhenChannelNotFound(): void
    {
        /** @var MockObject&ShopUserInterface $shopUser */
        $shopUser = $this->createMock(ShopUserInterface::class);
        $shopUser->method('isVerified')->willReturn(false);

        $this->shopUserRepository->expects(self::once())->method('findOneByEmail')->with('test@example.com')->willReturn($shopUser);
        $this->channelRepository->expects(self::once())->method('findOneByCode')->with('INVALID')->willReturn(null);
        $this->tokenGenerator->expects(self::never())->method('generate');
        $this->shopUserManager->expects(self::never())->method('flush');
        $this->emailSender->expects(self::never())->method('send');

        ($this->handler)(new ResendVerificationEmail(channelCode: 'INVALID', localeCode: 'en_US', email: 'test@example.com'));
    }
}
