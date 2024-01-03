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

namespace Sylius\Bundle\AdminBundle\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Command\ChangeAdminUserPasswordCommand;
use Sylius\Bundle\AdminBundle\Command\Factory\QuestionFactoryInterface;
use Sylius\Component\Core\Model\AdminUser;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Tester\CommandTester;

final class ChangeAdminUserPasswordCommandTest extends TestCase
{
    private const EMAIL = 'sylius@example.com';

    private const PASSWORD = 'Password';

    private CommandTester $command;

    private UserRepositoryInterface $userRepository;

    private PasswordUpdaterInterface $passwordUpdater;

    private QuestionFactoryInterface $questionFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->passwordUpdater = $this->createMock(PasswordUpdaterInterface::class);

        $this->questionFactory = $this->createMock(QuestionFactoryInterface::class);

        $this->command = new CommandTester(
            new ChangeAdminUserPasswordCommand($this->userRepository, $this->passwordUpdater, $this->questionFactory),
        );
    }

    /** @test */
    public function it_does_not_execute_in_non_interactive_mode(): void
    {
        $this->command->execute([], ['interactive' => false]);

        self::assertSame(Command::FAILURE, $this->command->getStatusCode());
    }

    /** @test */
    public function it_does_not_change_password_when_admin_user_is_not_found(): void
    {
        $this
            ->questionFactory
            ->expects($this->once())
            ->method('createEmail')
            ->willReturn($this->createQuestionMock('Email'));

        $this
            ->userRepository
            ->expects($this->once())
            ->method('findOneByEmail')
            ->willReturn(null);

        $this
            ->command
            ->setInputs([
                'email' => self::EMAIL
            ]);

        $this->command->execute([]);

        self::assertSame(Command::INVALID, $this->command->getStatusCode());
    }

    /** @test */
    public function it_changes_password_for_existing_admin_user(): void
    {
        $adminUser = $this->createMock(AdminUser::class);
        $adminUser
            ->expects($this->once())
            ->method('setPlainPassword')
            ->with(self::PASSWORD);

        $this
            ->questionFactory
            ->expects($this->once())
            ->method('createEmail')
            ->willReturn($this->createQuestionMock('Email'));

        $this
            ->questionFactory
            ->expects($this->once())
            ->method('createWithNotNullValidator')
            ->with('New password', true)
            ->willReturn($this->createQuestionMock('New password'));

        $this
            ->userRepository
            ->expects($this->once())
            ->method('findOneByEmail')
            ->willReturn($adminUser);
        $this
            ->userRepository
            ->expects($this->once())
            ->method('add')
            ->with($adminUser);

        $this
            ->passwordUpdater
            ->expects($this->once())
            ->method('updatePassword')
            ->with($adminUser);

        $this
            ->command
            ->setInputs([
                'email' => self::EMAIL,
                'password' => self::PASSWORD
            ]);

        $this->command->execute([]);

        self::assertSame(Command::SUCCESS, $this->command->getStatusCode());
    }

    private function createQuestionMock(string $askedQuestion): MockObject
    {
        $question = $this->createMock(Question::class);
        $question
            ->method('isTrimmable')
            ->willReturn(true);
        $question
            ->method('getQuestion')
            ->willReturn($askedQuestion);

        return $question;
    }
}
