<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\FixturesBundle\Command;

use Sylius\Bundle\FixturesBundle\Loader\SuiteLoaderInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteRegistryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

final class FixturesLoadCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sylius:fixtures:load')
            ->setDescription('Loads fixtures from given suite')
            ->addArgument('suite', InputArgument::OPTIONAL, 'Suite name', 'default')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        if ($input->isInteractive()) {
            /** @var QuestionHelper $questionHelper */
            $questionHelper = $this->getHelper('question');

            $output->writeln(sprintf(
                "\n<error>Warning! Loading fixtures will purge your database for the %s environment.</error>\n",
                $input->getOption('env')
            ));

            if (!$questionHelper->ask($input, $output, new ConfirmationQuestion('Continue? (y/N) ', false))) {
                return;
            }
        }

        $this->loadSuites($input);
    }

    /**
     * @param InputInterface $input
     */
    private function loadSuites(InputInterface $input): void
    {
        $suiteName = $input->getArgument('suite');
        $suite = $this->getSuiteRegistry()->getSuite($suiteName);

        $this->getSuiteLoader()->load($suite);
    }

    /**
     * @return SuiteRegistryInterface
     */
    private function getSuiteRegistry(): SuiteRegistryInterface
    {
        return $this->getContainer()->get('sylius_fixtures.suite_registry');
    }

    /**
     * @return SuiteLoaderInterface
     */
    private function getSuiteLoader(): SuiteLoaderInterface
    {
        return $this->getContainer()->get('sylius_fixtures.suite_loader');
    }
}
