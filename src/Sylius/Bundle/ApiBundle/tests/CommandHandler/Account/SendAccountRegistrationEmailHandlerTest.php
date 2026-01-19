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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler\Account;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Account\SendAccountRegistrationEmail;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\SendAccountRegistrationEmailHandler;
use Sylius\Bundle\CoreBundle\Mailer\AccountRegistrationEmailManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class SendAccountRegistrationEmailHandlerTest extends TestCase
{
    private MockObject&UserRepositoryInterface $shopUserRepository;

    private ChannelRepositoryInterface&MockObject $channelRepository;

    private AccountRegistrationEmailManagerInterface&MockObject $accountRegistrationEmailManager;

    private SendAccountRegistrationEmailHandler $handler;

    private MockObject&ShopUserInterface $shopUser;

    private ChannelInterface&MockObject $channel;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shopUserRepository = $this->createMock(UserRepositoryInterface::class);
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->accountRegistrationEmailManager = $this->createMock(AccountRegistrationEmailManagerInterface::class);
        $this->handler = new SendAccountRegistrationEmailHandler(
            $this->shopUserRepository,
            $this->channelRepository,
            $this->accountRegistrationEmailManager,
        );
        $this->shopUser = $this->createMock(ShopUserInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
    }

    public function testSendsUserAccountRegistrationEmailWhenAccountVerificationIsNotRequired(): void
    {
        $this->shopUserRepository->expects(self::once())
            ->method('findOneByEmail')
            ->with('shop@example.com')
            ->willReturn($this->shopUser);
        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('WEB')
            ->willReturn($this->channel);
        $this->channel->expects(self::once())->method('isAccountVerificationRequired')->willReturn(false);
        $this->accountRegistrationEmailManager->expects(self::once())
            ->method('sendAccountRegistrationEmail')
            ->with($this->shopUser, $this->channel, 'en_US');
        $this->handler->__invoke(new SendAccountRegistrationEmail('shop@example.com', 'en_US', 'WEB'));
    }

    public function testSendsUserRegistrationEmailWhenAccountVerificationRequiredAndUserIsEnabled(): void
    {
        $this->shopUserRepository->expects(self::once())
            ->method('findOneByEmail')
            ->with('shop@example.com')
            ->willReturn($this->shopUser);
        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('WEB')
            ->willReturn($this->channel);
        $this->channel->expects(self::once())->method('isAccountVerificationRequired')->willReturn(true);
        $this->shopUser->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->accountRegistrationEmailManager->expects(self::once())
            ->method('sendAccountRegistrationEmail')
            ->with($this->shopUser, $this->channel, 'en_US');
        $this->handler->__invoke(new SendAccountRegistrationEmail('shop@example.com', 'en_US', 'WEB'));
    }

    public function testDoesNothingWhenAccountVerificationIsRequiredAndUserIsDisabled(): void
    {
        $this->shopUserRepository->expects(self::once())
            ->method('findOneByEmail')
            ->with('shop@example.com')
            ->willReturn($this->shopUser);
        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('WEB')
            ->willReturn($this->channel);
        $this->channel->expects(self::once())->method('isAccountVerificationRequired')->willReturn(true);
        $this->shopUser->expects(self::once())->method('isEnabled')->willReturn(false);
        $this->accountRegistrationEmailManager->expects(self::never())
            ->method('sendAccountRegistrationEmail')
            ->with($this->any());
        $this->handler->__invoke(new SendAccountRegistrationEmail('shop@example.com', 'en_US', 'WEB'));
    }
}
