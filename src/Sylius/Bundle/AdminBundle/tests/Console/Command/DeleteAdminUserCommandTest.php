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

namespace Tests\Sylius\Bundle\AdminBundle\Console\Command;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

final class DeleteAdminUserCommandTest extends TestCase
{
    private CommandTester $commandTester;

    private MockObject&UserRepositoryInterface $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $command = new DeleteAdminUserCommand($this->userRepository);
        $this->commandTester = new CommandTester($command);
    }

    #[Test]
    public function it_fails_when_admin_user_does_not_exist(): void
    {
        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'missing@example.com'])
            ->willReturn(null);

        $exitCode = $this->commandTester->execute([
            'email' => 'missing@example.com',
        ], [
            'interactive' => false,
        ]);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString(
            'Admin Account with the email "missing@example.com" does not exist',
            $this->commandTester->getDisplay(),
        );
    }

    #[Test]
    public function it_deletes_existing_admin_user_successfully(): void
    {
        $adminUser = $this->createMock(AdminUserInterface::class);

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'admin@example.com'])
            ->willReturn($adminUser);

        $this->userRepository
            ->expects($this->once())
            ->method('remove')
            ->with($adminUser);

        $exitCode = $this->commandTester->execute([
            'email' => 'admin@example.com',
        ], [
            'interactive' => false,
        ]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString(
            'Admin Account with the email "admin@example.com" has been deleted successfully',
            $this->commandTester->getDisplay(),
        );
    }

    #[Test]
    public function it_requires_email_argument_in_non_interactive_mode(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "email").');

        $this->commandTester->execute([], [
            'interactive' => false,
        ]);
    }

    #[Test]
    public function it_aborts_when_user_declines_confirmation(): void
    {
        $adminUser = $this->createMock(AdminUserInterface::class);

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'decline@example.com'])
            ->willReturn($adminUser);

        $this->userRepository->expects($this->never())->method('remove');

        // simulate user typing "no" at confirmation
        $this->commandTester->setInputs(['no']);

        $exitCode = $this->commandTester->execute([
            'email' => 'decline@example.com',
        ], [
            'interactive' => true,
        ]);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString(
            'Are you sure you want to delete the admin user "decline@example.com"?',
            $this->commandTester->getDisplay(),
        );
    }
}
