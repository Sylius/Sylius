<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InstallerBundle\Command;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallDatabaseCommand extends AbstractInstallCommand
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
            $commands = [
                'doctrine:database:create',
                'doctrine:schema:create',
                'cache:clear',
                'doctrine:phpcr:repository:init',
            ];

            $this->runCommands($commands, $input, $output);

            return 0;
        }

        $dialog = $this->getHelper('dialog');
        $commands = [];

        if ($input->getOption('no-interaction')) {
            $commands['doctrine:schema:update'] = ['--force' => true];
        } else {
            if ($dialog->askConfirmation(
                $output,
                '<question>It appears that your database already exists. Would you like to reset it? (y/N)</question> ',
                false
            )
            ) {
                $commands['doctrine:database:drop'] = ['--force' => true];
                $commands[] = 'doctrine:database:create';
                $commands[] = 'doctrine:schema:create';
            } elseif ($this->isSchemaPresent()) {
                if ($dialog->askConfirmation(
                    $output,
                    '<question>Seems like your database contains schema. Do you want to reset it? (y/N)</question> ',
                    false
                )
                ) {
                    $commands['doctrine:schema:drop'] = ['--force' => true];
                    $commands[] = 'doctrine:schema:create';
                }
            }
        }

        $commands[] = 'cache:clear';
        $commands[] = 'doctrine:phpcr:repository:init';
        $commands[] = 'sylius:rbac:initialize';
        $commands['doctrine:migrations:version'] = [
            '--add' => true,
            '--all' => true,
            '--no-interaction' => true,
        ];

        $this->runCommands($commands, $input, $output);
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
        $schemaManager = $this->getSchemaManager();

        return $schemaManager->tablesExist(['sylius_user']);
    }

    /**
     * @return string
     */
    protected function getDatabaseName()
    {
        $databaseName = $this->getContainer()->getParameter('database_name');

        if ('prod' !== $this->getEnvironment()) {
            $databaseName = sprintf('%s_%s', $databaseName, $this->getEnvironment());
        }

        return $databaseName;
    }

    /**
     * @return AbstractSchemaManager
     */
    protected function getSchemaManager()
    {
        return $this->get('doctrine')->getManager()->getConnection()->getSchemaManager();
    }
}
