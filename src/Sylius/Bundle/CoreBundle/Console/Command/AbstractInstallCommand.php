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
use Sylius\Bundle\CoreBundle\Installer\Executor\CommandExecutor;
use SyliusLabs\Polyfill\Symfony\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractInstallCommand extends ContainerAwareCommand
{
    /** @deprecated */
    public const WEB_ASSETS_DIRECTORY = 'web/assets/';

    /** @deprecated */
    public const WEB_BUNDLES_DIRECTORY = 'web/bundles/';

    /** @deprecated */
    public const WEB_MEDIA_DIRECTORY = 'web/media/';

    /** @deprecated */
    public const WEB_MEDIA_IMAGE_DIRECTORY = 'web/media/image/';

    /** @var CommandExecutor|null */
    protected $commandExecutor;

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $application = $this->getApplication();
        $application->setCatchExceptions(false);

        $this->commandExecutor = new CommandExecutor($input, $output, $application);
    }

    /**
     * @return object
     */
    protected function get(string $id)
    {
        return $this->getContainer()->get($id);
    }

    protected function getEnvironment(): string
    {
        return (string) $this->getContainer()->getParameter('kernel.environment');
    }

    protected function isDebug(): bool
    {
        return (bool) $this->getContainer()->getParameter('kernel.debug');
    }

    /**
     * @param array<array-key, mixed>   $headers
     * @param array<array-key, mixed>   $rows
     */
    protected function renderTable(array $headers, array $rows, OutputInterface $output): void
    {
        $table = new Table($output);

        $table
            ->setHeaders($headers)
            ->setRows($rows)
            ->render()
        ;
    }

    protected function createProgressBar(OutputInterface $output, int $length = 10): ProgressBar
    {
        $progress = new ProgressBar($output);
        $progress->setBarCharacter('<info>░</info>');
        $progress->setEmptyBarCharacter(' ');
        $progress->setProgressCharacter('<comment>░</comment>');

        $progress->start($length);

        return $progress;
    }

    /** @param array<array-key, mixed> $commands */
    protected function runCommands(array $commands, OutputInterface $output, bool $displayProgress = true): void
    {
        $progress = $this->createProgressBar($displayProgress ? $output : new NullOutput(), count($commands));

        foreach ($commands as $key => $value) {
            if (is_string($key)) {
                $command = $key;
                $parameters = $value;
            } else {
                $command = $value;
                $parameters = [];
            }

            $this->commandExecutor->runCommand($command, $parameters);

            // PDO does not always close the connection after Doctrine commands.
            // See https://github.com/symfony/symfony/issues/11750.
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->getContainer()->get('doctrine')->getManager();
            $entityManager->getConnection()->close();

            $progress->advance();
        }

        $progress->finish();
    }

    protected function ensureDirectoryExistsAndIsWritable(string $directory, OutputInterface $output): void
    {
        $checker = $this->getContainer()->get('sylius.installer.checker.command_directory');
        $checker->setCommandName($this->getName());

        $checker->ensureDirectoryExists($directory, $output);
        $checker->ensureDirectoryIsWritable($directory, $output);
    }
}

class_alias(AbstractInstallCommand::class, '\Sylius\Bundle\CoreBundle\Command\AbstractInstallCommand');
