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

use Sylius\Bundle\CoreBundle\Installer\Executor\CommandExecutor;
use Sylius\Bundle\CoreBundle\Installer\Provider\DatabaseSetupCommandsProviderInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'sylius:install:database',
    description: 'Install Sylius database.',
)]
final class InstallDatabaseCommand extends Command
{
    public function __construct(
        private DatabaseSetupCommandsProviderInterface $databaseSetupCommandsProvider,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
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

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $commands = $this
            ->databaseSetupCommandsProvider
            ->getCommands($input, $output, $questionHelper)
        ;

        $commandExecutor = new CommandExecutor($input, $output, $this->getApplication());

        $commandExecutor->runCommands($commands, $output);
        $outputStyle->newLine();

        $parameters = [];
        if (null !== $suite) {
            $parameters['--fixture-suite'] = $suite;
        }

        $commandExecutor->runCommand('sylius:install:sample-data', $parameters, $output);

        return Command::SUCCESS;
    }

    private function getEnvironment(): string
    {
        /** @var Application $application */
        $application = $this->getApplication();

        return $application->getKernel()->getEnvironment();
    }
}
