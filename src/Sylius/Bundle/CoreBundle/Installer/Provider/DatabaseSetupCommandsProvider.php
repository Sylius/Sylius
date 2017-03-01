<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Installer\Provider;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class DatabaseSetupCommandsProvider implements DatabaseSetupCommandsProviderInterface
{
    /**
     * @var Registry
     */
    private $doctrineRegistry;

    /**
     * @param Registry $doctrineRegistry
     */
    public function __construct(Registry $doctrineRegistry)
    {
        $this->doctrineRegistry = $doctrineRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommands(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper)
    {
        if (!$this->isDatabasePresent()) {
            return [
                'doctrine:database:create',
                'doctrine:schema:create',
                'cache:clear',
            ];
        }

        return array_merge($this->getRequiredCommands($input, $output, $questionHelper), [
            'cache:clear',
            'doctrine:migrations:version' => [
                '--add' => true,
                '--all' => true,
                '--no-interaction' => true,
            ],
        ]);
    }

    /**
     * @return bool
     *
     * @throws \Exception
     */
    private function isDatabasePresent()
    {
        $databaseName = $this->getDatabaseName();

        try {
            $schemaManager = $this->getSchemaManager();
            
            return in_array($databaseName, $schemaManager->listDatabases());
        } catch (\Exception $exception) {
            $message = $exception->getMessage();

            $mysqlDatabaseError = false !== strpos($message, sprintf("Unknown database '%s'", $databaseName));
            $postgresDatabaseError = false !== strpos($message, sprintf('database "%s" does not exist', $databaseName));

            if ($mysqlDatabaseError || $postgresDatabaseError) {
                return false;
            }

            throw $exception;
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     *
     * @return array
     */
    private function getRequiredCommands(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper)
    {
        if ($input->getOption('no-interaction')) {
            $commands['doctrine:schema:update'] = ['--force' => true];
        }

        return $this->setupDatabase($input, $output, $questionHelper);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     *
     * @return array
     */
    private function setupDatabase(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper)
    {
        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('It appears that your database already exists.');
        $outputStyle->writeln('<error>Warning! This action will erase your database.</error>');

        $question = new ConfirmationQuestion('Would you like to reset it? (y/N) ', false);
        if ($questionHelper->ask($input, $output, $question)) {
            return [
                'doctrine:database:drop' => ['--force' => true],
                'doctrine:database:create',
                'doctrine:schema:create',
            ];
        }

        if (!$this->isSchemaPresent()) {
            return ['doctrine:schema:create'];
        }

        $outputStyle->writeln('Seems like your database contains schema.');
        $outputStyle->writeln('<error>Warning! This action will erase your database.</error>');
        $question = new ConfirmationQuestion('Do you want to reset it? (y/N) ', false);
        if ($questionHelper->ask($input, $output, $question)) {
            return [
                'doctrine:schema:drop' => ['--force' => true],
                'doctrine:schema:create',
            ];
        }

        return [];
    }

    /**
     * @return bool
     */
    private function isSchemaPresent()
    {
        return 0 !== count($this->getSchemaManager()->listTableNames());
    }

    /**
     * @return string
     */
    private function getDatabaseName()
    {
        return $this->doctrineRegistry->getManager()->getConnection()->getDatabase();
    }

    /**
     * @return AbstractSchemaManager
     */
    private function getSchemaManager()
    {
        return $this->doctrineRegistry->getManager()->getConnection()->getSchemaManager();
    }
}
