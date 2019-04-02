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

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\CoreBundle\Command\Helper\EnsureDirectoryExistsAndIsWritable;
use Sylius\Bundle\CoreBundle\Command\Helper\RunCommands;
use Sylius\Bundle\CoreBundle\Installer\Checker\CommandDirectoryChecker;
use Sylius\Bundle\CoreBundle\Installer\Executor\CommandExecutor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InstallSampleDataCommand extends Command
{
    use EnsureDirectoryExistsAndIsWritable {
        EnsureDirectoryExistsAndIsWritable::__construct as private initializeEnsureDirectoryExistsAndIsWritable;
    }
    use RunCommands {
        RunCommands::__construct as private initializeRunCommands;
    }

    /**
     * @var CommandDirectoryChecker
     */
    private $commandDirectoryChecker;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $publicDir;

    /**
     * @var string
     */
    private $environment;

    public function __construct(CommandDirectoryChecker $commandDirectoryChecker, EntityManagerInterface $entityManager, string $publicDir, string $environment)
    {
        $this->commandDirectoryChecker = $commandDirectoryChecker;
        $this->entityManager = $entityManager;
        $this->publicDir = $publicDir;
        $this->environment = $environment;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
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
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $commandExecutor = new CommandExecutor($input, $output, $this->getApplication());

        $this->initializeEnsureDirectoryExistsAndIsWritable($this->commandDirectoryChecker, $this->getName());
        $this->initializeRunCommands($commandExecutor, $this->entityManager);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->newLine();
        $outputStyle->writeln(sprintf(
            'Loading sample data for environment <info>%s</info>.',
            $this->environment
        ));
        $outputStyle->writeln('<error>Warning! This action will erase your database.</error>');

        if (!$questionHelper->ask($input, $output, new ConfirmationQuestion('Continue? (y/N) ', false))) {
            $outputStyle->writeln('Cancelled loading sample data.');

            return 0;
        }

        try {
            $this->ensureDirectoryExistsAndIsWritable($this->publicDir . '/media/', $output);
            $this->ensureDirectoryExistsAndIsWritable($this->publicDir . '/media/image/', $output);
        } catch (\RuntimeException $exception) {
            $outputStyle->writeln($exception->getMessage());

            return 1;
        }

        $commands = [
            'sylius:fixtures:load' => ['--no-interaction' => true],
        ];

        $this->runCommands($commands, $output);
        $outputStyle->newLine(2);

        return null;
    }
}
