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
use Symfony\Component\Process\Exception\RuntimeException;

class InstallCommand extends AbstractInstallCommand
{
    /**
     * @var array
     */
    private $commands = [
        [
            'command' => 'check-requirements',
            'message' => 'Checking system requirements.',
        ],
        [
            'command' => 'database',
            'message' => 'Setting up the database.',
        ],
        [
            'command' => 'setup',
            'message' => 'Shop configuration.',
        ],
        [
            'command' => 'assets',
            'message' => 'Installing assets.',
        ],
    ];

    /**
     * @var bool
     */
    private $isErrored = false;

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

        $this->ensureDirectoryExistsAndIsWritable(self::APP_CACHE, $output);

        foreach ($this->commands as $step => $command) {
            try {
                $output->writeln(sprintf('<comment>Step %d of %d.</comment> <info>%s</info>', $step + 1, count($this->commands), $command['message']));
                $this->commandExecutor->runCommand('sylius:install:'.$command['command'], [], $output);
                $output->writeln('');
            } catch (RuntimeException $exception) {
                $this->isErrored = true;

                continue;
            }
        }

        $frontControllerPath = 'prod' === $this->getEnvironment() ? '/' : sprintf('/app_%s.php', $this->getEnvironment());

        $output->writeln($this->getProperFinalMessage());
        $output->writeln(sprintf('You can now open your store at the following path under the website root: <info>%s.</info>', $frontControllerPath));
    }

    /**
     * @return string
     */
    private function getProperFinalMessage()
    {
        if ($this->isErrored) {
            return '<info>Sylius has been installed, but some error occurred.</info>';
        }

        return '<info>Sylius has been successfully installed.</info>';
    }
}
