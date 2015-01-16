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
            $commands = array(
                'doctrine:database:create',
                'doctrine:schema:create',
                'cache:clear',
                'doctrine:phpcr:repository:init'
            );

            $this->runCommands($commands, $input, $output);

            return 0;
        }

        $dialog = $this->getHelper('dialog');
        $commands = array();

        if ($dialog->askConfirmation(
            $output,
            '<question>It appears that your database already exists. Would you like to reset it? (Y/N)</question> ',
            false
        )) {
            $commands['doctrine:database:drop'] = array('--force' => true);
            $commands[] = 'doctrine:database:create';
            $commands[] = 'doctrine:schema:create';
        } elseif ($this->isSchemaPresent()) {
            if ($dialog->askConfirmation(
                $output,
                '<question>Seems like your database contains schema. Do you want to reset it? (Y/N)</question> ',
                false
            )) {
                $commands['doctrine:schema:drop'] = array('--force' => true);
                $commands[] = 'doctrine:schema:create';
            }
        } else {
            $commands[] = 'doctrine:schema:create';
        }

        $commands[] = 'doctrine:phpcr:repository:init';

        $this->runCommands($commands, $input, $output);

        $this->commandExecutor->runCommand('sylius:install:sample-data', array(), $output);
    }

    /**
     * @return bool
     */
    private function isDatabasePresent()
    {
        $schemaManager = $this->getSchemaManager();
        $databaseName = $this->getDatabaseName();

        return in_array($databaseName, $schemaManager->listDatabases());
    }

    /**
     * @return bool
     */
    private function isSchemaPresent()
    {
        $schemaManager = $this->getSchemaManager();

        return $schemaManager->tablesExist(array('sylius_user'));
    }

    /**
     * @return string
     */
    private function getDatabaseName()
    {
        $databaseName = $this->getContainer()->getParameter('sylius.database.name');

        if ('prod' !== $this->getEnvironment()) {
            $databaseName = sprintf('%s_%s', $databaseName, $this->getEnvironment());
        }

        return $databaseName;
    }

    /**
     * @return SchemaManager
     */
    private function getSchemaManager()
    {
        return $this->get('doctrine')->getManager()->getConnection()->getSchemaManager();
    }
}
