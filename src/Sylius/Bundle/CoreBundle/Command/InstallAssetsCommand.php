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
use Sylius\Bundle\CoreBundle\Command\Helper\DirectoryChecker;
use Sylius\Bundle\CoreBundle\Installer\Checker\CommandDirectoryChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class InstallAssetsCommand extends Command
{
    /**
     * @var DirectoryChecker
     */
    private $directoryChecker;

    /**
     * @var CommandsRunner
     */
    private $commandsRunner;

    /**
     * @var string
     */
    private $publicDir;

    /**
     * @var string
     */
    private $environment;

    public function __construct(
        DirectoryChecker $directoryChecker,
        CommandsRunner $commandsRunner,
        string $publicDir,
        string $environment
    ) {
        $this->directoryChecker = $directoryChecker;
        $this->commandsRunner = $commandsRunner;
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
            ->setName('sylius:install:assets')
            ->setDescription('Installs all Sylius assets.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command downloads and installs all Sylius media assets.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $output->writeln(sprintf(
            'Installing Sylius assets for environment <info>%s</info>.',
            $this->environment
        ));

        try {
            $this->directoryChecker->ensureDirectoryExistsAndIsWritable($this->publicDir . '/assets/', $output, $this->getName());
            $this->directoryChecker->ensureDirectoryExistsAndIsWritable($this->publicDir . '/bundles/', $output, $this->getName());
        } catch (\RuntimeException $exception) {
            $output->writeln($exception->getMessage());

            return 1;
        }

        $commands = [
            'assets:install' => ['target' => $this->publicDir],
        ];

        $this->commandsRunner->run($commands, $input, $output, $this->getApplication());

        return null;
    }
}
