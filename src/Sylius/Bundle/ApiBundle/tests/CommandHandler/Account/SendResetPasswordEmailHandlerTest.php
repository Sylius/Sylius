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
use Sylius\Bundle\ApiBundle\Command\Account\SendResetPasswordEmail;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\SendResetPasswordEmailHandler;
use Sylius\Bundle\CoreBundle\Mailer\ResetPasswordEmailManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class SendResetPasswordEmailHandlerTest extends TestCase
{
    private ChannelRepositoryInterface&MockObject $channelRepository;

    private MockObject&UserRepositoryInterface $userRepository;

    private MockObject&ResetPasswordEmailManagerInterface $resetPasswordEmailManager;

    private SendResetPasswordEmailHandler $handler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->resetPasswordEmailManager = $this->createMock(ResetPasswordEmailManagerInterface::class);
        $this->handler = new SendResetPasswordEmailHandler(
            $this->channelRepository,
            $this->userRepository,
            $this->resetPasswordEmailManager,
        );
    }

    public function testSendsMessageWithResetPasswordToken(): void
    {
        /** @var UserInterface|MockObject $user */
        $user = $this->createMock(UserInterface::class);
        /** @var ChannelInterface|MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        $this->userRepository->expects(self::once())
            ->method('findOneByEmail')
            ->with('iAmAnEmail@spaghettiCode.php')
            ->willReturn($user);
        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('WEB')->willReturn($channel);
        $this->resetPasswordEmailManager->expects(self::once())
            ->method('sendResetPasswordEmail')
            ->with($user, $channel, 'en_US');
        $this->handler->__invoke(new SendResetPasswordEmail('iAmAnEmail@spaghettiCode.php', 'WEB', 'en_US'));
    }
}
