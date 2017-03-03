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

final class InstallAssetsCommand extends AbstractInstallCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf(
            'Installing Sylius assets for environment <info>%s</info>.',
            $this->getEnvironment()
        ));

        try {
            $rootDir = $this->getContainer()->getParameter('kernel.root_dir') . '/../';
            $this->ensureDirectoryExistsAndIsWritable($rootDir . self::WEB_ASSETS_DIRECTORY, $output);
            $this->ensureDirectoryExistsAndIsWritable($rootDir . self::WEB_BUNDLES_DIRECTORY, $output);
        } catch (\RuntimeException $exception) {
            $output->writeln($exception->getMessage());

            return 1;
        }

        $commands = [
            'assets:install',
        ];

        $this->runCommands($commands, $output);
    }
}
