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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\MigrationSkipInterface;
use Sylius\Bundle\CoreBundle\EventListener\MigrationSkipListener;

final class MigrationSkipListenerTest extends TestCase
{
    use ProphecyTrait;

    private MetadataStorage|ObjectProphecy $metadataStorage;

    private DependencyFactory|ObjectProphecy $dependencyFactory;

    private MigrationSkipListener $listener;

    protected function setUp(): void
    {
        $this->metadataStorage = $this->prophesize(MetadataStorage::class);
        $this->dependencyFactory = $this->prophesize(DependencyFactory::class);

        $this->dependencyFactory->getMetadataStorage()->willReturn($this->metadataStorage);

        $this->listener = new MigrationSkipListener($this->dependencyFactory->reveal());
    }

    #[DataProvider('getInvalidSkipConditions')]
    #[Test]
    public function it_does_nothing_when_conditions_are_not_met(bool $isUp, bool $isMigrationSkip, bool $skipped): void
    {
        $this->dependencyFactory->getMetadataStorage()->shouldNotBeCalled();
        $this->metadataStorage->complete(Argument::any())->shouldNotBeCalled();

        $this->listener->onMigrationsVersionSkipped($this->createEvent(
            $isUp,
            $isMigrationSkip,
            $skipped,
        ));
    }

    #[Test]
    public function it_completed_the_skipped_migration(): void
    {
        $this->dependencyFactory->getMetadataStorage()->shouldBeCalled();
        $this->metadataStorage->complete(Argument::any())->shouldBeCalled();

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

        $migration = $this->prophesize(AbstractMigration::class);
        if ($isMigrationSkip) {
            $migration->willImplement(MigrationSkipInterface::class);
        }

        $plan = new MigrationPlan(
            $version,
            $migration->reveal(),
            $direction,
        );

        $plan->markAsExecuted($migrationResult);

        return new MigrationsVersionEventArgs(
            $this->prophesize(Connection::class)->reveal(),
            $plan,
            $this->prophesize(MigratorConfiguration::class)->reveal(),
        );
    }
}
