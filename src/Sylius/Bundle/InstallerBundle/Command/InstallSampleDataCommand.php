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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InstallSampleDataCommand extends AbstractInstallCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:install:sample-data')
            ->setDescription('Install sample data into Sylius.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command loads the sample data for Sylius.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('<error>Warning! This will erase your current database.</error> Your current environment is <info>%s</info>.', $this->getEnvironment()));

        if (!$this->getHelperSet()->get('dialog')->askConfirmation($output, '<question>Load sample data? Y/N</question> ', false)) {
            return;
        }

        $output->writeln('Loading sample data...');

        $doctrineConfiguration = $this->get('doctrine.orm.entity_manager')->getConnection()->getConfiguration();
        $logger = $doctrineConfiguration->getSQLLogger();
        $doctrineConfiguration->setSQLLogger(null);

        $commands = array(
            'doctrine:fixtures:load'       => array('--no-interaction' => true),
            'doctrine:phpcr:fixtures:load' => array('--no-interaction' => true)
        );

        $this->runCommands($commands, $input, $output);

        $doctrineConfiguration->setSQLLogger($logger);
    }
}