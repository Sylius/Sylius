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

use Sylius\Bundle\FixturesBundle\Loader\SuiteLoaderInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FixturesSuiteLoadCommand extends Command
{
    /**
     * @var SuiteRegistryInterface
     */
    private $suiteRegistry;

    /**
     * @var SuiteLoaderInterface
     */
    private $suiteLoader;

    /**
     * @param SuiteRegistryInterface $suiteRegistry
     * @param SuiteLoaderInterface $suiteLoader
     */
    public function __construct(SuiteRegistryInterface $suiteRegistry, SuiteLoaderInterface $suiteLoader)
    {
        parent::__construct('sylius:fixtures:suite:load');

        $this->suiteRegistry = $suiteRegistry;
        $this->suiteLoader = $suiteLoader;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Loads fixtures from given suite')
            ->addArgument('suite', InputArgument::REQUIRED, 'Suite name')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $suiteName = $input->getArgument('suite');

        $suite = $this->suiteRegistry->getSuite($suiteName);

        $this->suiteLoader->load($suite);
    }
}
