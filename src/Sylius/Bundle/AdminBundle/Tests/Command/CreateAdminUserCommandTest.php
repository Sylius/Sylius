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
use Sylius\Bundle\AdminBundle\Command\CreateAdminUserCommand;
use Sylius\Bundle\AdminBundle\Exception\CreateAdminUserFailedException;
use Sylius\Bundle\AdminBundle\Message\CreateAdminUser;
use Symfony\Component\Console\Command\Command;
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->messageBus = $this->createMock(MessageBusInterface::class);

        $this->command = new CommandTester(
            new CreateAdminUserCommand($this->messageBus, self::LOCALE_CODE),
        );
    }

    /** @test */
    public function it_creates_an_admin_user_if_accepted_in_the_summary(): void
    {
        $adminUserData = $this->getDefaultAdminUserDataSetup();

        $commandInputs = $this->getDefaultCommandInputsSetup();

        $this->assertSuccessfulCommandExecution($adminUserData, $commandInputs);
    }

    /** @test */
    public function it_has_set_up_three_attempts_to_write_a_valid_email(): void
    {
        $adminUserData = $this->getDefaultAdminUserDataSetup();

        $commandInputs = array_merge(
            [
                'first_email_entry' => 'invalid-email',
                'second_email_entry' => 'still-invalid-email',
            ],
            $this->getDefaultCommandInputsSetup(),
        );

        $this->assertSuccessfulCommandExecution($adminUserData, $commandInputs);
    }

    /** @test */
    public function it_has_set_up_three_attempts_to_write_a_non_blank_username(): void
    {
        $adminUserData = $this->getDefaultAdminUserDataSetup();

        $commandInputs = [
            'email' => self::EMAIL,
            'first_username_entry' => '',
            'second_username_entry' => '',
            'username' => self::USERNAME,
            'first_name' => self::FIRST_NAME,
            'last_name' => self::LAST_NAME,
            'password' => self::PASSWORD,
            'locale_code' => self::LOCALE_CODE,
            'admin_user_enabled' => self::YES,
            'creation_confirmation' => self::YES,
        ];

        $this->assertSuccessfulCommandExecution($adminUserData, $commandInputs);
    }

    /** @test */
    public function it_has_set_up_three_attempts_to_write_a_non_blank_password(): void
    {
        $adminUserData = $this->getDefaultAdminUserDataSetup();

        $commandInputs = [
            'email' => self::EMAIL,
            'username' => self::USERNAME,
            'first_name' => self::FIRST_NAME,
            'last_name' => self::LAST_NAME,
            'first_password_entry' => '',
            'second_password_entry' => '',
            'password' => self::PASSWORD,
            'locale_code' => self::LOCALE_CODE,
            'admin_user_enabled' => self::YES,
            'creation_confirmation' => self::YES,
        ];

        $this->assertSuccessfulCommandExecution($adminUserData, $commandInputs);
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

    private function assertSuccessfulCommandExecution(array $adminUserData, array $commandInputs): void
    {
        $this->command->setInputs($commandInputs);

        $message = new CreateAdminUser(...array_values($adminUserData));

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($message)
            ->willReturn(new Envelope($message, [new HandledStamp(self::anything(), 'handler')]))
        ;

        $this->command->execute([]);

        $this->command->assertCommandIsSuccessful();
        self::assertStringContainsString('Admin user has been successfully created.', $this->command->getDisplay());
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
}
