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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Event\MigrationsVersionEventArgs;
use Doctrine\Migrations\Metadata\MigrationPlan;
use Doctrine\Migrations\Metadata\Storage\MetadataStorage;
use Doctrine\Migrations\MigratorConfiguration;
use Doctrine\Migrations\Version\Direction;
use Doctrine\Migrations\Version\ExecutionResult;
use Doctrine\Migrations\Version\Version;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\MigrationSkipInterface;
use Sylius\Bundle\CoreBundle\EventListener\MigrationSkipListener;

#[AllowMockObjectsWithoutExpectations]
final class MigrationSkipListenerTest extends TestCase
{
    private MetadataStorage&MockObject $metadataStorage;

    private DependencyFactory&MockObject $dependencyFactory;

    private MigrationSkipListener $listener;

    protected function setUp(): void
    {
        $this->metadataStorage = $this->createMock(MetadataStorage::class);
        $this->dependencyFactory = $this->createMock(DependencyFactory::class);

        $this->dependencyFactory
            ->method('getMetadataStorage')
            ->willReturn($this->metadataStorage);

        $this->listener = new MigrationSkipListener($this->dependencyFactory);
    }

    #[DataProvider('getInvalidSkipConditions')]
    #[Test]
    public function it_does_nothing_when_conditions_are_not_met(bool $isUp, bool $isMigrationSkip, bool $skipped): void
    {
        $this->metadataStorage
            ->expects($this->never())
            ->method('complete');

        $this->listener->onMigrationsVersionSkipped($this->createEvent(
            $isUp,
            $isMigrationSkip,
            $skipped,
        ));
    }

    #[Test]
    public function it_completed_the_skipped_migration(): void
    {
        $this->metadataStorage
            ->expects($this->once())
            ->method('complete');

        $this->listener->onMigrationsVersionSkipped($this->createEvent(true, true, true));
    }

    public static function getInvalidSkipConditions(): iterable
    {
        yield 'down migration' => [false, true, true];
        yield 'down migration, not skipped' => [false, true, false];
        yield 'down migration, not skip interface' => [false, false, true];
        yield 'down migration, not skip interface, not skipped' => [false, false, false];
        yield 'up migration, not skipped' => [true, true, false];
        yield 'up migration, not skip interface' => [true, false, true];
        yield 'up migration, not skip interface, not skipped' => [true, false, false];
    }

    private function createEvent(
        bool $isUp,
        bool $isMigrationSkip,
        bool $skipped,
    ): MigrationsVersionEventArgs {
        $version = new Version('test');
        $direction = $isUp ? Direction::UP : Direction::DOWN;

        $migrationResult = new ExecutionResult($version, $direction);
        $migrationResult->setSkipped($skipped);

        if ($isMigrationSkip) {
            $migration = new class($this->createMock(Connection::class), $this->createMock(LoggerInterface::class)) extends AbstractMigration implements MigrationSkipInterface {
                public function up(\Doctrine\DBAL\Schema\Schema $schema): void
                {
                }

                public function down(\Doctrine\DBAL\Schema\Schema $schema): void
                {
                }
            };
        } else {
            $migration = new class($this->createMock(Connection::class), $this->createMock(LoggerInterface::class)) extends AbstractMigration {
                public function up(\Doctrine\DBAL\Schema\Schema $schema): void
                {
                }

                public function down(\Doctrine\DBAL\Schema\Schema $schema): void
                {
                }
            };
        }

        $plan = new MigrationPlan(
            $version,
            $migration,
            $direction,
        );

        $plan->markAsExecuted($migrationResult);

        return new MigrationsVersionEventArgs(
            $this->createMock(Connection::class),
            $plan,
            $this->createMock(MigratorConfiguration::class),
        );
    }
}
