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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
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
        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln(sprintf(
            'Creating Sylius database for environment <info>%s</info>.',
            $this->getEnvironment()
        ));

        $commands = $this
            ->get('sylius.commands_provider.database_setup')
            ->getCommands($input, $output, $this->getHelper('question'))
        ;

        $this->runCommands($commands, $output);
        $outputStyle->newLine();

        $this->commandExecutor->runCommand('sylius:install:sample-data', [], $output);
    }
}
