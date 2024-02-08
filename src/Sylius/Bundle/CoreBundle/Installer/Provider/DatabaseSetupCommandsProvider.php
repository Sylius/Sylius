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

namespace Sylius\Bundle\CoreBundle\Installer\Provider;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

final class DatabaseSetupCommandsProvider implements DatabaseSetupCommandsProviderInterface
{
    private bool $isPostgreSQLPlatform;

    public function __construct(private Registry $doctrineRegistry)
    {
        $this->isPostgreSQLPlatform = $this->isPostgreSQLPlatform();
    }

    public function getCommands(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): array
    {
        $outputStyle = new SymfonyStyle($input, $output);

        if ($this->isSchemaHasAnyTable()) {
            $outputStyle->writeln(sprintf('The database <info>%s</info> exists and it contains some tables.', $this->getDatabaseName()));
            $outputStyle->writeln('<error>Warning! This action will erase your database.</error>');

            $question = new ConfirmationQuestion('Do you want to drop all of them? (y/N) ', false);
            if ($questionHelper->ask($input, $output, $question)) {
                return $this->dropSchemaAndGetMigrateOrSchemaCreateCommands($outputStyle);
            }

            return [];
        }

        if ($this->isEmptyDatabasePresent()) {
            $outputStyle->writeln(
                sprintf('The database <info>%s</info> already exists and it has no tables.', $this->getDatabaseName()),
            );

            return $this->getCreateSchemaOrRunMigrationsCommand($outputStyle);
        }

        return $this->getCreateDatabaseWithSchemaCommands($outputStyle);
    }

    private function isEmptyDatabasePresent(): bool
    {
        try {
            return 0 === count($this->getSchemaManager()->listTableNames());
        } catch (\Exception) {
            return false;
        }
    }

    private function isSchemaHasAnyTable(): bool
    {
        try {
            return 0 !== count($this->getSchemaManager()->listTableNames());
        } catch (\Exception) {
            return false;
        }
    }

    private function getDatabaseName(): string
    {
        return $this->getEntityManager()->getConnection()->getDatabase();
    }

    private function isPostgreSQLPlatform(): bool
    {
        return $this->getEntityManager()->getConnection()->getDatabasePlatform() instanceof PostgreSQLPlatform;
    }

    private function getSchemaManager(): AbstractSchemaManager
    {
        $connection = $this->getEntityManager()->getConnection();

        if (method_exists($connection, 'createSchemaManager')) {
            return $connection->createSchemaManager();
        }

        if (method_exists($connection, 'getSchemaManager')) {
            return $connection->getSchemaManager();
        }

        throw new \RuntimeException('Unable to get schema manager.');
    }

    private function getEntityManager(): EntityManagerInterface
    {
        $objectManager = $this->doctrineRegistry->getManager();
        Assert::isInstanceOf($objectManager, EntityManagerInterface::class);

        return $objectManager;
    }

    private function getCreateDatabaseWithSchemaCommands(SymfonyStyle $outputStyle): array
    {
        if ($this->isPostgreSQLPlatform) {
            $outputStyle->writeln([
                'As you\'re using PostgreSQL, we will create a database and schema instead of running migrations.',
                'They will be available starting from Sylius 1.13.',
            ]);

            return [
                'doctrine:database:create',
                'doctrine:schema:create',
            ];
        }

        return [
            'doctrine:database:create',
            'doctrine:migrations:migrate' => ['--no-interaction' => true],
        ];
    }

    /** To refactor in Sylius 1.13 */
    private function getCreateSchemaOrRunMigrationsCommand(SymfonyStyle $outputStyle): array
    {
        if ($this->isPostgreSQLPlatform) {
            $outputStyle->writeln([
                'As you\'re using PostgreSQL, we will create a schema instead of running migrations.',
                'They will be available starting from Sylius 1.13.',
            ]);

            return ['doctrine:schema:create'];
        }

        return ['doctrine:migrations:migrate' => ['--no-interaction' => true]];
    }

    /** To refactor in Sylius 1.13 */
    private function dropSchemaAndGetMigrateOrSchemaCreateCommands(SymfonyStyle $outputStyle): array
    {
        if ($this->isPostgreSQLPlatform) {
            $outputStyle->writeln([
                'As you\'re using PostgreSQL, we will drop and create a schema instead of running migrations.',
                'They will be available starting from Sylius 1.13.',
            ]);

            return [
                'doctrine:schema:drop' => ['--force' => true],
                'doctrine:schema:create',
            ];
        }

        return [
            'doctrine:schema:drop' => ['--force' => true],
            'doctrine:migrations:version' => ['--delete' => true, '--all' => true, '--no-interaction' => true],
            'doctrine:migrations:migrate' => ['--no-interaction' => true],
        ];
    }
}
