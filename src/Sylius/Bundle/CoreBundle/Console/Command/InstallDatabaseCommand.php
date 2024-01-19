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

namespace Sylius\Bundle\CoreBundle\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InstallDatabaseCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'sylius:install:database';

    protected function configure(): void
    {
        $this
            ->setDescription('Install Sylius database.')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command creates Sylius database.
EOT
            )
            ->addOption('fixture-suite', 's', InputOption::VALUE_OPTIONAL, 'Load specified fixture suite during install', null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $suite = $input->getOption('fixture-suite');

        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln(sprintf(
            'Creating Sylius database for environment <info>%s</info>.',
            $this->getEnvironment(),
        ));

        $commands = $this
            ->getContainer()
            ->get('sylius.commands_provider.database_setup')
            ->getCommands($input, $output, $this->getHelper('question'))
        ;

        $this->runCommands($commands, $output);
        $outputStyle->newLine();

        $parameters = [];
        if (null !== $suite) {
            $parameters['--fixture-suite'] = $suite;
        }
        $this->commandExecutor->runCommand('sylius:install:sample-data', $parameters, $output);

        return 0;
    }
}

class_alias(InstallDatabaseCommand::class, '\Sylius\Bundle\CoreBundle\Command\InstallDatabaseCommand');
