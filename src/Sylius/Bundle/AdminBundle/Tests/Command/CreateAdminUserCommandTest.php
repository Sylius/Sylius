<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Command\CreateAdminUserCommand;
use Sylius\Component\Core\Model\AdminUser;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class CreateAdminUserCommandTest extends TestCase
{
    private MockObject $userRepository;

    private MockObject $factory;

    private MockObject $canonicalizer;

    private CommandTester $command;

    /** @test */
    public function it_creates_an_admin_user(): void
    {
        $this->canonicalizer
            ->method('canonicalize')
            ->with('SYLius@exaMPLE.com')
            ->willReturn('sylius@example.com')
        ;

        $this->userRepository
            ->method('findOneByEmail')
            ->with('sylius@example.com')
            ->willReturn(null)
        ;

        $this->factory->method('createNew')->willReturn(new AdminUser());

        $this->command->setInputs([
            'Do you want to create an admin user ?' => 'yes',
            'Email' => 'SYLius@exaMPLE.com',
            'Username' => 'Sylius',
            'Firstname' => 'Sylius',
            'Lastname' => 'Admin',
            'Password' => 'sylius',
        ]);

        self::assertEquals(Command::SUCCESS, $this->command->execute([]));
    }

    /** @test */
    public function it_does_not_create_an_admin_user_if_user_already_exists(): void
    {
        $this->canonicalizer
            ->method('canonicalize')
            ->with('SYLius@exaMPLE.com')
            ->willReturn('sylius@example.com')
        ;

        $adminUser = new AdminUser();

        $this->userRepository
            ->method('findOneByEmail')
            ->with('sylius@example.com')
            ->willReturn($adminUser);

        $this->command->setInputs([
            'Do you want to create an admin user ?' => 'yes',
            'Email' => 'SYLius@exaMPLE.com',
            'Username' => 'Sylius',
            'Firstname' => 'Sylius',
            'Lastname' => 'Admin',
            'Password' => 'sylius',
        ]);

        self::assertEquals(Command::INVALID, $this->command->execute([]));
    }

    /** @test */
    public function it_throws_an_exception_if_provided_email_is_not_valid(): void
    {
        $this->command->setInputs(['Email' => 'invalid-email']);

        self::expectException(\Exception::class);

        $this->command->execute([]);
    }

    /** @test */
    public function it_does_not_create_an_admin_user_if_command_is_not_interactive(): void
    {
        self::assertEquals(Command::FAILURE, $this->command->execute([], ['interactive' => false]));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->getMockBuilder(UserRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->factory = $this->getMockBuilder(FactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->canonicalizer = $this->getMockBuilder(CanonicalizerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->command = new CommandTester(new CreateAdminUserCommand(
            $this->userRepository,
            $this->factory,
            $this->canonicalizer,
        ));
    }
}
