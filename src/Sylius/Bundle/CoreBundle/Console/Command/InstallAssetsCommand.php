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

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\CoreBundle\Installer\Checker\CommandDirectoryChecker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'sylius:install:assets',
    description: 'Installs all Sylius assets.',
)]
final class InstallAssetsCommand extends AbstractInstallCommand
{
    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        protected readonly CommandDirectoryChecker $commandDirectoryChecker,
        protected readonly string $publicDir,
    ) {
        parent::__construct($this->entityManager, $this->commandDirectoryChecker);
    }

    protected function configure(): void
    {
        $this
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command downloads and installs all Sylius media assets.
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(sprintf(
            'Installing Sylius assets for environment <info>%s</info>.',
            $this->getEnvironment(),
        ));

        try {
            $this->ensureDirectoryExistsAndIsWritable($this->publicDir . '/assets/', $output);
            $this->ensureDirectoryExistsAndIsWritable($this->publicDir . '/bundles/', $output);
        } catch (\RuntimeException $exception) {
            $output->writeln($exception->getMessage());

            return Command::FAILURE;
        }

        $commands = [
            'assets:install' => ['target' => $this->publicDir],
        ];

        $this->runCommands($commands, $output);

        return Command::SUCCESS;
    }
}
