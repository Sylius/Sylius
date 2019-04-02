<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Command;

use Sylius\Bundle\CoreBundle\Command\Helper\CommandsRunner;
use Sylius\Bundle\CoreBundle\Installer\Executor\CommandExecutor;
use Sylius\Bundle\CoreBundle\Installer\Provider\DatabaseSetupCommandsProviderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InstallDatabaseCommand extends Command
{
    /**
     * @var DatabaseSetupCommandsProviderInterface
     */
    private $databaseSetupCommandsProvider;

    /**
     * @var CommandsRunner
     */
    private $commandsRunner;

    /**
     * @var string
     */
    private $environment;

    public function __construct(
        DatabaseSetupCommandsProviderInterface $databaseSetupCommandsProvider,
        CommandsRunner $commandsRunner,
        string $environment
    ) {
        $this->databaseSetupCommandsProvider = $databaseSetupCommandsProvider;
        $this->commandsRunner = $commandsRunner;
        $this->environment = $environment;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
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
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->commandExecutor = new CommandExecutor($input, $output, $this->getApplication());
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln(sprintf(
            'Creating Sylius database for environment <info>%s</info>.',
            $this->environment
        ));

        $commands = $this
            ->databaseSetupCommandsProvider
            ->getCommands($input, $output, $this->getHelper('question'))
        ;

        $this->commandsRunner->run($commands, $input, $output, $this->getApplication());
        $outputStyle->newLine();

        $this->commandExecutor->runCommand('sylius:install:sample-data', [], $output);
    }
}
