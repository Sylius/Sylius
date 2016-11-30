<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Installer\Executor;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * @author Romain Monceau <romain@akeneo.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class CommandExecutor
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Application $application
     */
    public function __construct(InputInterface $input, OutputInterface $output, Application $application)
    {
        $this->input = $input;
        $this->output = $output;
        $this->application = $application;
    }

    /**
     * @param string $command
     * @param array $parameters
     * @param OutputInterface $output
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function runCommand($command, $parameters = [], OutputInterface $output = null)
    {
        $parameters = array_merge(
            ['command' => $command],
            $this->getDefaultParameters(),
            $parameters
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
     * @return array
     */
    protected function getDefaultParameters()
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
}
