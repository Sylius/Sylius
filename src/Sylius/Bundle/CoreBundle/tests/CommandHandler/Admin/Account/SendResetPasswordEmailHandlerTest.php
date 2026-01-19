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

namespace Tests\Sylius\Bundle\CoreBundle\CommandHandler\Admin\Account;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Command\Admin\Account\SendResetPasswordEmail;
use Sylius\Bundle\CoreBundle\CommandHandler\Admin\Account\SendResetPasswordEmailHandler;
use Sylius\Bundle\CoreBundle\Mailer\ResetPasswordEmailManagerInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

final class SendResetPasswordEmailHandlerTest extends TestCase
{
    private MockObject&UserRepositoryInterface $userRepository;

    private MockObject&ResetPasswordEmailManagerInterface $resetPasswordEmailManager;

    private SendResetPasswordEmailHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->resetPasswordEmailManager = $this->createMock(ResetPasswordEmailManagerInterface::class);
        $this->handler = new SendResetPasswordEmailHandler(
            $this->userRepository,
            $this->resetPasswordEmailManager,
        );
    }

    public function testItIsAMessageHandler(): void
    {
        $reflection = new \ReflectionClass(SendResetPasswordEmailHandler::class);
        $attributes = $reflection->getAttributes(AsMessageHandler::class);

        $this->assertCount(1, $attributes);
    }

    public function testItHandlesSendingResetPasswordEmail(): void
    {
        $adminUser = $this->createMock(AdminUserInterface::class);

        $this->userRepository
            ->method('findOneByEmail')
            ->with('admin@example.com')
            ->willReturn($adminUser)
        ;

        $this->resetPasswordEmailManager->expects($this->never())->method('sendResetPasswordEmail');

        $this->resetPasswordEmailManager
            ->expects($this->once())
            ->method('sendAdminResetPasswordEmail')
            ->with($adminUser, 'en_US')
        ;

        ($this->handler)(new SendResetPasswordEmail('admin@example.com', 'en_US'));
    }

    public function testItThrowsExceptionWhileHandlingIfUserDoesntExist(): void
    {
        $this->userRepository
            ->method('findOneByEmail')
            ->with('admin@example.com')
            ->willReturn(null)
        ;

        $this->resetPasswordEmailManager->expects($this->never())->method('sendResetPasswordEmail');
        $this->resetPasswordEmailManager->expects($this->never())->method('sendAdminResetPasswordEmail');

        $this->expectException(\InvalidArgumentException::class);

        ($this->handler)(new SendResetPasswordEmail('admin@example.com', 'en_US'));
    }
}
