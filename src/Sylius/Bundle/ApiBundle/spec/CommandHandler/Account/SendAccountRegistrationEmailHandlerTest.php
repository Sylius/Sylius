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
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Bundle\ApiBundle\Command\Account\SendAccountRegistrationEmail;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\SendAccountRegistrationEmailHandler;
use Sylius\Bundle\CoreBundle\Mailer\AccountRegistrationEmailManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

final class SendAccountRegistrationEmailHandlerTest extends TestCase
{
    /** @var UserRepositoryInterface|MockObject */
    private MockObject $shopUserRepositoryMock;

    /** @var ChannelRepositoryInterface|MockObject */
    private MockObject $channelRepositoryMock;

    /** @var AccountRegistrationEmailManagerInterface|MockObject */
    private MockObject $accountRegistrationEmailManagerMock;

    private SendAccountRegistrationEmailHandler $sendAccountRegistrationEmailHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->shopUserRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->channelRepositoryMock = $this->createMock(ChannelRepositoryInterface::class);
        $this->accountRegistrationEmailManagerMock = $this->createMock(AccountRegistrationEmailManagerInterface::class);
        $this->sendAccountRegistrationEmailHandler = new SendAccountRegistrationEmailHandler($this->shopUserRepositoryMock, $this->channelRepositoryMock, $this->accountRegistrationEmailManagerMock);
    }

    public function testSendsUserAccountRegistrationEmailWhenAccountVerificationIsNotRequired(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $this->shopUserRepositoryMock->expects(self::once())->method('findOneByEmail')->with('shop@example.com')->willReturn($shopUserMock);
        $this->channelRepositoryMock->expects(self::once())->method('findOneByCode')->with('WEB')->willReturn($channelMock);
        $channelMock->expects(self::once())->method('isAccountVerificationRequired')->willReturn(false);
        $this->accountRegistrationEmailManagerMock->expects(self::once())->method('sendAccountRegistrationEmail')->with($shopUserMock, $channelMock, 'en_US')
        ;
        $this(new SendAccountRegistrationEmail('shop@example.com', 'en_US', 'WEB'));
    }

    public function testSendsUserRegistrationEmailWhenAccountVerificationRequiredAndUserIsEnabled(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $this->shopUserRepositoryMock->expects(self::once())->method('findOneByEmail')->with('shop@example.com')->willReturn($shopUserMock);
        $this->channelRepositoryMock->expects(self::once())->method('findOneByCode')->with('WEB')->willReturn($channelMock);
        $channelMock->expects(self::once())->method('isAccountVerificationRequired')->willReturn(true);
        $shopUserMock->expects(self::once())->method('isEnabled')->willReturn(true);
        $this->accountRegistrationEmailManagerMock->expects(self::once())->method('sendAccountRegistrationEmail')->with($shopUserMock, $channelMock, 'en_US')
        ;
        $this(new SendAccountRegistrationEmail('shop@example.com', 'en_US', 'WEB'));
    }

    public function testDoesNothingWhenAccountVerificationIsRequiredAndUserIsDisabled(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $this->shopUserRepositoryMock->expects(self::once())->method('findOneByEmail')->with('shop@example.com')->willReturn($shopUserMock);
        $this->channelRepositoryMock->expects(self::once())->method('findOneByCode')->with('WEB')->willReturn($channelMock);
        $channelMock->expects(self::once())->method('isAccountVerificationRequired')->willReturn(true);
        $shopUserMock->expects(self::once())->method('isEnabled')->willReturn(false);
        $this->accountRegistrationEmailManagerMock->expects(self::never())->method('sendAccountRegistrationEmail')->with($this->any());
        $this(new SendAccountRegistrationEmail('shop@example.com', 'en_US', 'WEB'));
    }
}
