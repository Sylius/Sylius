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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractInstallCommand extends ContainerAwareCommand
{
    /**
     * @var CommandExecutor
     */
    protected $commandExecutor;

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->commandExecutor = new CommandExecutor(
            $input,
            $output,
            $this->getApplication()
        );
    }

    /**
     * @param $id
     *
     * @return object
     */
    protected function get($id)
    {
        return $this->getContainer()->get($id);
    }

    /**
     * @return string
     */
    protected function getEnvironment()
    {
        return $this->get('kernel')->getEnvironment();
    }

    /**
     * @return boolean
     */
    protected function isDebug()
    {
        return $this->get('kernel')->isDebug();
    }

    /**
     * @param array $headers
     * @param array $rows
     * @param OutputInterface $output
     */
    protected function renderTable(array $headers, array $rows, OutputInterface $output)
    {
        $table = $this->getHelper('table');

        $table
            ->setHeaders($headers)
            ->setRows($rows)
            ->render($output)
        ;
    }

    /**
     * @param OutputInterface $output
     * @param int $length
     */
    protected function createProgressBar(OutputInterface $output, $length = 10)
    {
        $progress = $this->getHelper('progress');
        $progress->setBarCharacter('<info>|</info>');
        $progress->setEmptyBarCharacter(' ');
        $progress->setProgressCharacter('|');

        $progress->start($output, $length);

        return $progress;
    }

    /**
     * @param array $commands
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param boolean $displayProgress
     */
    protected function runCommands(array $commands, InputInterface $input, OutputInterface $output, $displayProgress = true)
    {
        if ($displayProgress) {
            $progress = $this->createProgressBar($output, count($commands));
        }

        foreach ($commands as $key => $value) {
            if (is_string($key)) {
                $command = $key;
                $parameters = $value;
            } else {
                $command = $value;
                $parameters = array();
            }

            $this->commandExecutor->runCommand($command, $parameters);

            // PDO does not always close the connection after Doctrine commands.
            // See https://github.com/symfony/symfony/issues/11750.
            $this->get('doctrine')->getManager()->getConnection()->close();

            if ($displayProgress) {
                $progress->advance();
            }
        }

        if ($displayProgress) {
            $progress->finish();
        }
    }

    /**
     * @param string $question
     */
    protected function askRequired(OutputInterface $output, $question)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        do {
            $value = $dialog->ask($output, sprintf('<question>%s</question> ', $question));

            if (empty($value)) {
                $output->writeln('<error>Please enter correct value.</error>');
            }
        } while (empty($value));

        return $value;
    }
}
