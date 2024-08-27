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

namespace Sylius\Bundle\CoreBundle\Installer\Executor;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\RuntimeException;

final class CommandExecutor
{
    public function __construct(private InputInterface $input, private OutputInterface $output, private Application $application)
    {
    }

    /**
     * @throws \Exception
     */
    public function runCommand(string $command, array $parameters = [], ?OutputInterface $output = null): self
    {
        $parameters = array_merge(
            ['command' => $command],
            $this->getDefaultParameters(),
            $parameters,
        );

        $this->application->setAutoExit(false);
        $exitCode = $this->application->run(new ArrayInput($parameters), $output ?: new NullOutput());

        if (1 === $exitCode) {
            throw new RuntimeException('This command terminated with a permission error.');
        }

        if (0 !== $exitCode) {
            $this->application->setAutoExit(true);

            $errorMessage = sprintf('The command terminated with an error code: %u.', $exitCode);
            $this->output->writeln("<error>$errorMessage</error>");

            throw new \Exception($errorMessage, $exitCode);
        }

        return $this;
    }

    /**
     * @param string[] $commands
     */
    public function runCommands(array $commands, OutputInterface $output, bool $displayProgress = true): void
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

            $this->runCommand($command, $parameters);

            $progress->advance();
        }

        $progress->finish();
    }

    private function getDefaultParameters(): array
    {
        $defaultParameters = ['--no-debug' => true];

        if ($this->input->hasOption('env')) {
            $defaultParameters['--env'] = $this->input->hasOption('env') ? $this->input->getOption('env') : 'dev';
        }

        if ($this->input->hasOption('no-interaction') && true === $this->input->getOption('no-interaction')) {
            $defaultParameters['--no-interaction'] = true;
        }

        if ($this->input->hasOption('verbose') && true === $this->input->getOption('verbose')) {
            $defaultParameters['--verbose'] = true;
        }

        return $defaultParameters;
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
}
