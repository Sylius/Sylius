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

use Sylius\Bundle\CoreBundle\Kernel\Kernel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends AbstractInstallCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:install')
            ->setDescription('Installs Sylius in your preferred environment.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command installs Sylius.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Installing Sylius...</info>');
        $output->writeln('');

        $output->writeln('<comment>Step 1 of 4.</comment> <info>Checking system requirements.</info>');
        $this->commandExecutor->runCommand('sylius:install:check-requirements', array(), $output);
        $output->writeln('');

        $output->writeln('<comment>Step 2 of 4.</comment> <info>Setting up the database.</info>');
        $this->commandExecutor->runCommand('sylius:install:database', array(), $output);
        $output->writeln('');

        $output->writeln('<comment>Step 3 of 4.</comment> <info>Shop configuration.</info>');
        $this->commandExecutor->runCommand('sylius:install:setup', array(), $output);
        $output->writeln('');

        $output->writeln('<comment>Step 4 of 4.</comment> <info>Installing assets.</info>');
        $this->commandExecutor->runCommand('sylius:install:assets', array(), $output);
        $output->writeln('');

        $map = array(
            Kernel::ENV_DEV => '/app_dev.php',
            Kernel::ENV_TEST => '/app_test.php',
            Kernel::ENV_STAGING => '/app_staging.php',
            Kernel::ENV_PROD => '/'
        );

        $output->writeln('<info>Sylius has been successfully installed.</info>');
        $output->writeln(sprintf('You can now open your store at the following path under the website root: <info>%s.</info>', $map[$this->getEnvironment()]));
    }
}
