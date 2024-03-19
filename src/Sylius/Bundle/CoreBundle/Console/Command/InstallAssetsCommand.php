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
use Symfony\Component\Console\Output\OutputInterface;

final class InstallAssetsCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'sylius:install:assets';

    protected function configure(): void
    {
        $this
            ->setDescription('Installs all Sylius assets.')
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
            $publicDir = $this->getContainer()->getParameter('sylius_core.public_dir');

            $this->ensureDirectoryExistsAndIsWritable($publicDir . '/assets/', $output);
            $this->ensureDirectoryExistsAndIsWritable($publicDir . '/bundles/', $output);
        } catch (\RuntimeException $exception) {
            $output->writeln($exception->getMessage());

            return 1;
        }

        $commands = [
            'assets:install' => ['target' => $publicDir],
        ];

        $this->runCommands($commands, $output);

        return 0;
    }
}

class_alias(InstallAssetsCommand::class, '\Sylius\Bundle\CoreBundle\Command\InstallAssetsCommand');
