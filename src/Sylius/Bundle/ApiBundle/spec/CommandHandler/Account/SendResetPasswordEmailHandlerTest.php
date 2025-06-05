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
use Sylius\Bundle\ApiBundle\Command\Account\SendResetPasswordEmail;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\SendResetPasswordEmailHandler;
use Sylius\Bundle\CoreBundle\Mailer\ResetPasswordEmailManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

final class SendResetPasswordEmailHandlerTest extends TestCase
{
    /** @var ChannelRepositoryInterface|MockObject */
    private MockObject $channelRepositoryMock;

    /** @var UserRepositoryInterface|MockObject */
    private MockObject $userRepositoryMock;

    /** @var ResetPasswordEmailManagerInterface|MockObject */
    private MockObject $resetPasswordEmailManagerMock;

    private SendResetPasswordEmailHandler $sendResetPasswordEmailHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->channelRepositoryMock = $this->createMock(ChannelRepositoryInterface::class);
        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->resetPasswordEmailManagerMock = $this->createMock(ResetPasswordEmailManagerInterface::class);
        $this->sendResetPasswordEmailHandler = new SendResetPasswordEmailHandler($this->channelRepositoryMock, $this->userRepositoryMock, $this->resetPasswordEmailManagerMock);
    }

    public function testSendsMessageWithResetPasswordToken(): void
    {
        /** @var UserInterface|MockObject $userMock */
        $userMock = $this->createMock(UserInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $this->userRepositoryMock->expects(self::once())->method('findOneByEmail')->with('iAmAnEmail@spaghettiCode.php')->willReturn($userMock);
        $this->channelRepositoryMock->expects(self::once())->method('findOneByCode')->with('WEB')->willReturn($channelMock);
        $this->resetPasswordEmailManagerMock->expects(self::once())->method('sendResetPasswordEmail')->with($userMock, $channelMock, 'en_US');
        $this(new SendResetPasswordEmail('iAmAnEmail@spaghettiCode.php', 'WEB', 'en_US'));
    }
}
