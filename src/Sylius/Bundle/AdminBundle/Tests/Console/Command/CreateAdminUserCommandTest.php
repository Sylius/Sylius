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

namespace Sylius\Bundle\AdminBundle\Tests\Console\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Command\CreateAdminUser;
use Sylius\Bundle\AdminBundle\Console\Command\Factory\QuestionFactoryInterface;
use Sylius\Bundle\AdminBundle\Console\Command\CreateAdminUserCommand;
use Sylius\Bundle\AdminBundle\Exception\CreateAdminUserFailedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class CreateAdminUserCommandTest extends TestCase
{
    private const EMAIL = 'sylius@example.com';

    private const LOCALE_CODE = 'en_US';

    private const USERNAME = 'Username';

    private const PASSWORD = 'Password';

    private const FIRST_NAME = 'First name';

    private const LAST_NAME = 'Last name';

    private const YES = 'yes';

    private const NO = 'no';

    private CommandTester $command;

    private MockObject $messageBus;

    private QuestionFactoryInterface $questionFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->questionFactory = $this->createMock(QuestionFactoryInterface::class);

        $this->command = new CommandTester(
            new CreateAdminUserCommand($this->messageBus, self::LOCALE_CODE, $this->questionFactory),
        );
    }

    /** @test */
    public function it_creates_an_admin_user_if_accepted_in_the_summary(): void
    {
        $this
            ->questionFactory
            ->expects($this->once())
            ->method('createEmail')
            ->willReturn($this->createQuestionMock('Email'));

        $this
            ->questionFactory
            ->expects($this->exactly(2))
            ->method('createWithNotNullValidator')
            ->willReturnOnConsecutiveCalls(
                $this->createQuestionMock('Username'),
                $this->createQuestionMock('New password'),
            );

        $this->command->setInputs($this->getDefaultCommandInputsSetup());

        $message = new CreateAdminUser(...array_values($this->getDefaultAdminUserDataSetup()));

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($message)
            ->willReturn(new Envelope($message, [new HandledStamp(self::anything(), 'handler')]))
        ;

        $this->command->execute([]);

        $this->command->assertCommandIsSuccessful();
        self::assertStringContainsString('Admin user has been successfully created.', $this->command->getDisplay());
    }

    /** @test */
    public function it_does_not_create_an_admin_user_if_declined_in_the_summary(): void
    {
        $this->command->setInputs([
            'email' => self::EMAIL,
            'username' => self::USERNAME,
            'firstname' => 'Sylius',
            'lastname' => 'Admin',
            'password' => 'sylius',
            'localeCode' => self::LOCALE_CODE,
            'admin_user_enabled' => self::YES,
            'creation_confirmation' => self::NO,
        ]);

        $this
            ->questionFactory
            ->expects($this->once())
            ->method('createEmail')
            ->willReturn($this->createQuestionMock('Email'));

        $this
            ->questionFactory
            ->expects($this->exactly(2))
            ->method('createWithNotNullValidator')
            ->willReturnOnConsecutiveCalls(
                $this->createQuestionMock('Username'),
                $this->createQuestionMock('New password'),
            );

        $this->messageBus->expects($this->never())->method('dispatch');

        self::assertSame(Command::INVALID, $this->command->execute([]));
        self::assertStringContainsString('Admin user creation has been aborted.', $this->command->getDisplay());
    }

    /** @test */
    public function it_does_not_create_an_admin_user_if_dispatched_command_returns_failure(): void
    {
        $adminUserData = $this->getDefaultAdminUserDataSetup();

        $this->command->setInputs($this->getDefaultCommandInputsSetup());

        $message = new CreateAdminUser(...array_values($adminUserData));

        $this
            ->questionFactory
            ->expects($this->once())
            ->method('createEmail')
            ->willReturn($this->createQuestionMock('Email'));

        $this
            ->questionFactory
            ->expects($this->exactly(2))
            ->method('createWithNotNullValidator')
            ->willReturnOnConsecutiveCalls(
                $this->createQuestionMock('Username'),
                $this->createQuestionMock('New password'),
            );

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($message)
            ->willThrowException(new HandlerFailedException(
                new Envelope($message),
                [new CreateAdminUserFailedException('Some validation error')],
            ))
        ;

        $this->command->execute([]);

        self::assertSame(Command::FAILURE, $this->command->getStatusCode());
    }

    /** @test */
    public function it_does_not_create_an_admin_user_if_command_is_not_interactive(): void
    {
        self::assertSame(Command::FAILURE, $this->command->execute([], ['interactive' => false]));
    }

    private function getDefaultCommandInputsSetup(): array
    {
        return [
            'email' => self::EMAIL,
            'username' => self::USERNAME,
            'first_name' => self::FIRST_NAME,
            'last_name' => self::LAST_NAME,
            'password' => self::PASSWORD,
            'locale_code' => self::LOCALE_CODE,
            'admin_user_enabled' => self::YES,
            'creation_confirmation' => self::YES,
        ];
    }

    private function getDefaultAdminUserDataSetup(): array
    {
        return [
            'email' => self::EMAIL,
            'username' => self::USERNAME,
            'first_name' => self::FIRST_NAME,
            'last_name' => self::LAST_NAME,
            'password' => self::PASSWORD,
            'locale_code' => self::LOCALE_CODE,
            'admin_user_enabled' => true,
        ];
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
