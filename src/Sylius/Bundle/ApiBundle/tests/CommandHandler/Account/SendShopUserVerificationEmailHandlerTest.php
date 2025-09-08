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
use Sylius\Bundle\ApiBundle\Command\Account\SendShopUserVerificationEmail;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\SendShopUserVerificationEmailHandler;
use Sylius\Bundle\ApiBundle\Exception\ChannelNotFoundException;
use Sylius\Bundle\ApiBundle\Exception\UserNotFoundException;
use Sylius\Bundle\CoreBundle\Mailer\AccountVerificationEmailManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class SendShopUserVerificationEmailHandlerTest extends TestCase
{
    private MockObject&UserRepositoryInterface $shopUserRepository;

    private ChannelRepositoryInterface&MockObject $channelRepository;

    private AccountVerificationEmailManagerInterface&MockObject $accountVerificationEmailManager;

    private SendShopUserVerificationEmailHandler $handler;

    private MockObject&ShopUserInterface $shopUser;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shopUserRepository = $this->createMock(UserRepositoryInterface::class);
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->accountVerificationEmailManager = $this->createMock(AccountVerificationEmailManagerInterface::class);
        $this->handler = new SendShopUserVerificationEmailHandler(
            $this->shopUserRepository,
            $this->channelRepository,
            $this->accountVerificationEmailManager,
        );
        $this->shopUser = $this->createMock(ShopUserInterface::class);
    }

    public function testSendsUserAccountVerificationEmail(): void
    {
        /** @var ChannelInterface|MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        $this->shopUserRepository->expects(self::once())
            ->method('findOneByEmail')
            ->with('shop@example.com')
            ->willReturn($this->shopUser);
        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('WEB')
            ->willReturn($channel);
        $this->accountVerificationEmailManager->expects(self::once())
            ->method('sendAccountVerificationEmail')
            ->with($this->shopUser, $channel, 'en_US');
        $this->handler->__invoke(new SendShopUserVerificationEmail('shop@example.com', 'en_US', 'WEB'));
    }

    public function testThrowsAnExceptionIfUserHasNotBeenFound(): void
    {
        $this->shopUserRepository->expects(self::once())
            ->method('findOneByEmail')
            ->with('shop@example.com')
            ->willReturn(null);
        $this->channelRepository->expects(self::never())
            ->method('findOneByCode')
            ->with('WEB');
        $this->accountVerificationEmailManager->expects(self::never())
            ->method('sendAccountVerificationEmail')
            ->with($this->any());
        self::expectException(UserNotFoundException::class);
        $this->handler->__invoke(new SendShopUserVerificationEmail('shop@example.com', 'en_US', 'WEB'));
    }

    public function testThrowsAnExceptionIfChannelHasNotBeenFound(): void
    {
        $this->shopUserRepository->expects(self::once())
            ->method('findOneByEmail')
            ->with('shop@example.com')
            ->willReturn($this->shopUser);
        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('WEB')
            ->willReturn(null);
        $this->accountVerificationEmailManager->expects(self::never())
            ->method('sendAccountVerificationEmail')
            ->with($this->any());
        self::expectException(ChannelNotFoundException::class);
        $this->handler->__invoke(new SendShopUserVerificationEmail('shop@example.com', 'en_US', 'WEB'));
    }
}
