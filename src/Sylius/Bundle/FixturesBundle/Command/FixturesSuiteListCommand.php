<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Command;

use Sylius\Bundle\FixturesBundle\Suite\SuiteRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FixturesSuiteListCommand extends Command
{
    /**
     * @var SuiteRegistryInterface
     */
    private $suiteRegistry;

    /**
     * @param SuiteRegistryInterface $suiteRegistry
     */
    public function __construct(SuiteRegistryInterface $suiteRegistry)
    {
        parent::__construct('sylius:fixtures:suite:list');

        $this->suiteRegistry = $suiteRegistry;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Lists available suites')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $suites = $this->suiteRegistry->getSuites();

        $output->writeln('Available suites:');

        foreach ($suites as $suite) {
            $output->writeln(' - ' . $suite->getName());
        }
    }
}
