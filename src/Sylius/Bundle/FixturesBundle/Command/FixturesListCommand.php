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

use Sylius\Bundle\FixturesBundle\Fixture\FixtureRegistryInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteRegistryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FixturesListCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sylius:fixtures:list')
            ->setDescription('Lists available fixtures')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->listSuites($output);
        $this->listFixtures($output);
    }

    /**
     * @param OutputInterface $output
     */
    private function listSuites(OutputInterface $output): void
    {
        $suites = $this->getSuiteRegistry()->getSuites();

        $output->writeln('Available suites:');

        foreach ($suites as $suite) {
            $output->writeln(' - ' . $suite->getName());
        }
    }

    /**
     * @param OutputInterface $output
     */
    private function listFixtures(OutputInterface $output): void
    {
        $fixtures = $this->getFixtureRegistry()->getFixtures();

        $output->writeln('Available fixtures:');

        foreach ($fixtures as $name => $fixture) {
            $output->writeln(' - ' . $name);
        }
    }

    /**
     * @return SuiteRegistryInterface
     */
    private function getSuiteRegistry(): SuiteRegistryInterface
    {
        return $this->getContainer()->get('sylius_fixtures.suite_registry');
    }

    /**
     * @return FixtureRegistryInterface
     */
    private function getFixtureRegistry(): FixtureRegistryInterface
    {
        return $this->getContainer()->get('sylius_fixtures.fixture_registry');
    }
}
