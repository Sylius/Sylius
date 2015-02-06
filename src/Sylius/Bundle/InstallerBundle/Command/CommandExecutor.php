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
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command executor
 *
 * @author Romain Monceau <romain@akeneo.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CommandExecutor
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
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Application     $application
     */
    public function __construct(InputInterface $input, OutputInterface $output, Application $application)
    {
        $this->input = $input;
        $this->output = $output;
        $this->application = $application;
    }

    /**
     * @param $command
     * @param array $parameters
     *
     * @throws \Exception
     */
    public function runCommand($command, $parameters = array(), OutputInterface $output = null)
    {
        $parameters = array_merge(
            array('command' => $command),
            $parameters,
            $this->getDefaultParameters()
        );

        $this->application->setAutoExit(false);
        $exitCode = $this->application->run(new ArrayInput($parameters), $output ?: new NullOutput());

        if (0 !== $exitCode) {
            $this->application->setAutoExit(true);

            $errorMessage = sprintf('The command terminated with an error code: %u.', $exitCode);
            $this->output->writeln("<error>$errorMessage</error>");
            $exception = new \Exception($errorMessage, $exitCode);

            throw $exception;
        }

        return $this;
    }

    /**
     * Get default parameters.
     *
     * @return array
     */
    protected function getDefaultParameters()
    {
        $defaultParameters = array('--no-debug' => true);

        if ($this->input->hasOption('env')) {
            $defaultParameters['--env'] = $this->input->hasOption('env') ? $this->input->getOption('env') : Kernel::ENV_DEV;
        }

        if ($this->input->hasOption('verbose') && true === $this->input->getOption('verbose')) {
            $defaultParameters['--verbose'] = true;
        }

        return $defaultParameters;
    }
}