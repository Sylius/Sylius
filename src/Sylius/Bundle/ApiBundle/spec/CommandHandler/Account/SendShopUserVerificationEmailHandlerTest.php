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
use Sylius\Bundle\ApiBundle\Command\Account\SendShopUserVerificationEmail;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\SendShopUserVerificationEmailHandler;
use Sylius\Bundle\ApiBundle\Exception\ChannelNotFoundException;
use Sylius\Bundle\ApiBundle\Exception\UserNotFoundException;
use Sylius\Bundle\CoreBundle\Mailer\AccountVerificationEmailManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

final class SendShopUserVerificationEmailHandlerTest extends TestCase
{
    /** @var UserRepositoryInterface|MockObject */
    private MockObject $shopUserRepositoryMock;

    /** @var ChannelRepositoryInterface|MockObject */
    private MockObject $channelRepositoryMock;

    /** @var AccountVerificationEmailManagerInterface|MockObject */
    private MockObject $accountVerificationEmailManagerMock;

    private SendShopUserVerificationEmailHandler $sendShopUserVerificationEmailHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->shopUserRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->channelRepositoryMock = $this->createMock(ChannelRepositoryInterface::class);
        $this->accountVerificationEmailManagerMock = $this->createMock(AccountVerificationEmailManagerInterface::class);
        $this->sendShopUserVerificationEmailHandler = new SendShopUserVerificationEmailHandler($this->shopUserRepositoryMock, $this->channelRepositoryMock, $this->accountVerificationEmailManagerMock);
    }

    public function testSendsUserAccountVerificationEmail(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $this->shopUserRepositoryMock->expects(self::once())->method('findOneByEmail')->with('shop@example.com')->willReturn($shopUserMock);
        $this->channelRepositoryMock->expects(self::once())->method('findOneByCode')->with('WEB')->willReturn($channelMock);
        $channelMock->expects(self::once())->method('isAccountVerificationRequired')->willReturn(false);
        $this->accountVerificationEmailManagerMock->expects(self::once())->method('sendAccountVerificationEmail')->with($shopUserMock, $channelMock, 'en_US')
        ;
        $this(new SendShopUserVerificationEmail('shop@example.com', 'en_US', 'WEB'));
    }

    public function testThrowsAnExceptionIfUserHasNotBeenFound(): void
    {
        $this->shopUserRepositoryMock->expects(self::once())->method('findOneByEmail')->with('shop@example.com')->willReturn(null);
        $this->channelRepositoryMock->expects(self::never())->method('findOneByCode')->with('WEB');
        $this->accountVerificationEmailManagerMock->expects(self::never())->method('sendAccountVerificationEmail')->with($this->any());
        $this->expectException(UserNotFoundException::class);
        $this->sendShopUserVerificationEmailHandler->__invoke(new SendShopUserVerificationEmail('shop@example.com', 'en_US', 'WEB'));
    }

    public function testThrowsAnExceptionIfChannelHasNotBeenFound(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        $this->shopUserRepositoryMock->expects(self::once())->method('findOneByEmail')->with('shop@example.com')->willReturn($shopUserMock);
        $this->channelRepositoryMock->expects(self::once())->method('findOneByCode')->with('WEB')->willReturn(null);
        $this->accountVerificationEmailManagerMock->expects(self::never())->method('sendAccountVerificationEmail')->with($this->any());
        $this->expectException(ChannelNotFoundException::class);
        $this->sendShopUserVerificationEmailHandler->__invoke(new SendShopUserVerificationEmail('shop@example.com', 'en_US', 'WEB'));
    }
}
