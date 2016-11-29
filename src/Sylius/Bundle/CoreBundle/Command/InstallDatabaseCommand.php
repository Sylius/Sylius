<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Command;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

final class InstallDatabaseCommand extends AbstractInstallCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:install:database')
            ->setDescription('Install Sylius database.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates Sylius database.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Creating Sylius database for environment <info>%s</info>.', $this->getEnvironment()));

        if (!$this->isDatabasePresent()) {
            return $this->createAndFillDatabase($output);
        }

        $commands = [];

        if ($input->getOption('no-interaction')) {
            $commands['doctrine:schema:update'] = ['--force' => true];
        } else {
            $commands = array_merge($commands, $this->setupDatabase($input, $output));
        }

        $commands = array_merge($commands, [
            'cache:clear',
            'doctrine:migrations:version' => [
                '--add' => true,
                '--all' => true,
                '--no-interaction' => true,
            ],
        ]);

        $this->runCommands($commands, $output);
        $output->writeln('');

        $this->commandExecutor->runCommand('sylius:install:sample-data', [], $output);
    }

    /**
     * @return bool
     *
     * @throws \Exception
     */
    protected function isDatabasePresent()
    {
        $databaseName = $this->getDatabaseName();

        try {
            $schemaManager = $this->getSchemaManager();
        } catch (\Exception $exception) {
            $message = $exception->getMessage();

            $mysqlDatabaseError = false !== strpos($message, sprintf("Unknown database '%s'", $databaseName));
            $postgresDatabaseError = false !== strpos($message, sprintf('database "%s" does not exist', $databaseName));

            if ($mysqlDatabaseError || $postgresDatabaseError) {
                return false;
            }

            throw $exception;
        }

        return in_array($databaseName, $schemaManager->listDatabases());
    }

    /**
     * @return bool
     */
    protected function isSchemaPresent()
    {
        return 0 !== count($this->getSchemaManager()->listTableNames());
    }

    /**
     * @return string
     */
    protected function getDatabaseName()
    {
        return $this->get('doctrine')->getManager()->getConnection()->getDatabase();
    }

    /**
     * @return AbstractSchemaManager
     */
    protected function getSchemaManager()
    {
        return $this->get('doctrine')->getManager()->getConnection()->getSchemaManager();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return array
     */
    protected function setupDatabase(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $question = new ConfirmationQuestion('It appears that your database already exists. Would you like to reset it? (y/N)', false);
        if ($questionHelper->ask($input, $output, $question)) {
            return [
                'doctrine:database:drop' => ['--force' => true],
                'doctrine:database:create',
                'doctrine:schema:create',
            ];
        }

        if (!$this->isSchemaPresent()) {
            return [
                'doctrine:schema:create',
            ];
        }

        $question = new ConfirmationQuestion('Seems like your database contains schema. Do you want to reset it? (y/N)', false);
        if ($questionHelper->ask($input, $output, $question)) {
            return [
                'doctrine:schema:drop' => ['--force' => true],
                'doctrine:schema:create',
            ];
        }

        return [];
    }

    /**
     * @param OutputInterface $output
     *
     * @return CommandExecutor
     */
    private function createAndFillDatabase(OutputInterface $output)
    {
        $commands = [
            'doctrine:database:create',
            'doctrine:schema:create',
            'cache:clear',
        ];

        $this->runCommands($commands, $output);
        $output->writeln('');

        return $this->commandExecutor->runCommand('sylius:install:sample-data', [], $output);
    }
}
