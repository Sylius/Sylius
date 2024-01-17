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

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InstallSampleDataCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'sylius:install:sample-data';

    protected function configure(): void
    {
        $this
            ->setDescription('Install sample data into Sylius.')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command loads the sample data for Sylius.
EOT
            )
            ->addOption('fixture-suite', 's', InputOption::VALUE_OPTIONAL, 'Load specified fixture suite during install', null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        $suite = $input->getOption('fixture-suite');

        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->newLine();
        $outputStyle->writeln(sprintf(
            'Loading sample data for environment <info>%s</info> from suite <info>%s</info>.',
            $this->getEnvironment(),
            $suite ?? 'default',
        ));
        $outputStyle->writeln('<error>Warning! This action will erase your database.</error>');

        if (!$questionHelper->ask($input, $output, new ConfirmationQuestion('Continue? (y/N) ', null !== $suite))) {
            $outputStyle->writeln('Cancelled loading sample data.');

            return 0;
        }

        try {
            $publicDir = $this->getContainer()->getParameter('sylius_core.public_dir');

            $this->ensureDirectoryExistsAndIsWritable($publicDir . '/media/', $output);
            $this->ensureDirectoryExistsAndIsWritable($publicDir . '/media/image/', $output);
        } catch (\RuntimeException $exception) {
            $outputStyle->writeln($exception->getMessage());

            return 1;
        }

        $parameters = ['--no-interaction' => true];

        if (null !== $suite) {
            $parameters['suite'] = $suite;
        }

        $commands = [
            'sylius:fixtures:load' => $parameters,
        ];

        $this->runCommands($commands, $output);
        $outputStyle->newLine(2);

        return 0;
    }
}

class_alias(InstallSampleDataCommand::class, '\Sylius\Bundle\CoreBundle\Command\InstallSampleDataCommand');
