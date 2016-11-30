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

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class InstallSampleDataCommand extends AbstractInstallCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:install:sample-data')
            ->setDescription('Install sample data into Sylius.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command loads the sample data for Sylius.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $output->writeln(sprintf(
            '<error>Warning! This will erase your database.</error> Your current environment is <info>%s</info>.',
            $this->getEnvironment()
        ));

        if (!$questionHelper->ask($input, $output, new ConfirmationQuestion('Load sample data? (y/N) ', false))) {
            $output->writeln('Cancelled loading sample data.');

            return 0;
        }

        $output->writeln('Loading sample data...');

        try {
            $rootDir = $this->getContainer()->getParameter('kernel.root_dir') . '/../';
            $this->ensureDirectoryExistsAndIsWritable($rootDir . self::WEB_MEDIA_DIRECTORY, $output);
            $this->ensureDirectoryExistsAndIsWritable($rootDir . self::WEB_MEDIA_IMAGE_DIRECTORY, $output);
        } catch (\RuntimeException $exception) {
            $output->writeln($exception->getMessage());

            return 1;
        }

        $commands = [
            'sylius:fixtures:load' => ['--no-interaction' => true],
        ];

        $this->runCommands($commands, $output);
    }
}
