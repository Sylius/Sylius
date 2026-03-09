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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Console\Command\ListAdminUsersCommand;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class ListAdminUsersCommandTest extends TestCase
{
    private CommandTester $commandTester;

    /** @var MockObject&UserRepositoryInterface<AdminUserInterface> */
    private MockObject $userRepository;

    /** @var MockObject&EntityManagerInterface */
    private MockObject $entityManager;

    /** @var MockObject&QueryBuilder */
    private MockObject $queryBuilder;

    /** @var MockObject&Query */
    private MockObject $query;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->query = $this->createMock(Query::class);

        $entityManager = $this->entityManager;
        $command = new ListAdminUsersCommand($this->userRepository, $entityManager);

        $this->commandTester = new CommandTester($command);
    }

    #[Test]
    public function it_lists_all_admin_users(): void
    {
        $adminUser = $this->createConfiguredMock(AdminUserInterface::class, [
            'getId' => 1,
            'getEmail' => 'admin@example.com',
            'getUsername' => 'admin',
            'getFirstName' => 'John',
            'getLastName' => 'Doe',
            'getLocaleCode' => 'en_US',
            'isEnabled' => true,
            'getRoles' => ['ROLE_ADMINISTRATION_ACCESS'],
        ]);

        $this->userRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$adminUser]);

        $exitCode = $this->commandTester->execute([]);

        self::assertSame(Command::SUCCESS, $exitCode);
        $output = $this->commandTester->getDisplay();

        self::assertStringContainsString('List available admin users', $output);
        self::assertStringContainsString('admin@example.com', $output);
        self::assertStringContainsString('John', $output);
        self::assertStringContainsString('ADMINISTRATION_ACCESS', $output);
    }

    #[Test]
    public function it_displays_empty_table_when_no_admin_users_exist(): void
    {
        $this->userRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $exitCode = $this->commandTester->execute([]);

        self::assertSame(Command::SUCCESS, $exitCode);
        $output = $this->commandTester->getDisplay();

        self::assertStringContainsString('List available admin users', $output);
        self::assertStringContainsString('ID', $output);
        self::assertStringContainsString('E-Mail', $output);
    }

    #[Test]
    public function it_filters_users_by_search_term(): void
    {
        $matchingUser = $this->createConfiguredMock(AdminUserInterface::class, [
            'getId' => 2,
            'getEmail' => 'sylius@example.com',
            'getUsername' => 'sylius',
            'getFirstName' => 'Luke',
            'getLastName' => 'Brushwood',
            'getLocaleCode' => 'en_US',
            'isEnabled' => true,
            'getRoles' => ['ROLE_ADMINISTRATION_ACCESS'],
        ]);

        $this->entityManager->method('createQueryBuilder')->willReturn($this->queryBuilder);
        $this->queryBuilder->method('from')->willReturnSelf();
        $this->queryBuilder->method('select')->willReturnSelf();
        $this->queryBuilder->method('where')->willReturnSelf();
        $this->queryBuilder->method('andWhere')->willReturnSelf();
        $this->queryBuilder->method('setParameter')->willReturnSelf();
        $this->queryBuilder->method('orderBy')->willReturnSelf();
        $this->queryBuilder->method('getQuery')->willReturn($this->query);

        $this->query->method('getResult')->willReturn([$matchingUser]);

        $exitCode = $this->commandTester->execute(['--search' => 'sylius']);

        self::assertSame(Command::SUCCESS, $exitCode);
        $output = $this->commandTester->getDisplay();

        self::assertStringContainsString('Found 1 user(s) matching "sylius"', $output);
        self::assertStringContainsString('sylius@example.com', $output);
    }

    #[Test]
    public function it_shows_zero_results_when_no_user_matches_search_term(): void
    {
        $this->entityManager->method('createQueryBuilder')->willReturn($this->queryBuilder);
        $this->queryBuilder->method('from')->willReturnSelf();
        $this->queryBuilder->method('select')->willReturnSelf();
        $this->queryBuilder->method('where')->willReturnSelf();
        $this->queryBuilder->method('andWhere')->willReturnSelf();
        $this->queryBuilder->method('setParameter')->willReturnSelf();
        $this->queryBuilder->method('orderBy')->willReturnSelf();
        $this->queryBuilder->method('getQuery')->willReturn($this->query);

        $this->query->method('getResult')->willReturn([]);

        $exitCode = $this->commandTester->execute(['--search' => 'ghost']);
        self::assertSame(Command::SUCCESS, $exitCode);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('No users found matching "ghost"', $output);
    }
}
