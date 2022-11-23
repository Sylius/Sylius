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

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Command\CreateAdminUserCommand;
use Sylius\Component\Core\Model\AdminUser;
use Sylius\Component\Locale\Model\Locale;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class CreateAdminUserCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_an_admin_user(): void
    {
        $this->userRepository
            ->method('findOneByEmail')
            ->with('sylius@example.com')
            ->willReturn(null);

        $this->factory->method('createNew')->willReturn(new AdminUser());

        $locale = new Locale();
        $locale->setCode('en_US');
        $this->localeRepository->method('findAll')->willReturn([$locale]);

        $this->command->setInputs([
            'Do you want to create an admin user ?' => 'yes',
            'Email' => 'sylius@example.com',
            'Username' => 'Sylius',
            'Firstname' => 'Sylius',
            'Lastname' => 'Admin',
            'Password' => 'sylius',
        ]);

        self::assertEquals(Command::SUCCESS, $this->command->execute([]));
    }

    /**
     * @test
     */
    public function it_does_not_create_an_admin_user_if_user_already_exists(): void
    {
        $adminUser = new AdminUser();

        $this->userRepository
            ->method('findOneByEmail')
            ->with('sylius@example.com')
            ->willReturn($adminUser);

        $this->command->setInputs([
            'Email' => 'sylius@example.com',
        ]);

        self::assertEquals(Command::INVALID, $this->command->execute([]));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->getMockBuilder(UserRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->localeRepository = $this->getMockBuilder(RepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->factory = $this->getMockBuilder(FactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->command = new CommandTester(new CreateAdminUserCommand(
            $this->userRepository,
            $this->localeRepository,
            $this->factory
        ));
    }
}
