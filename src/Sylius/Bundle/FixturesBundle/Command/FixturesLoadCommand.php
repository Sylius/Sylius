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
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FixturesLoadCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $suiteName = $input->getArgument('suite');

        $suite = $this->getSuiteRegistry()->getSuite($suiteName);

        $this->getSuiteLoader()->load($suite);
    }

    /**
     * @return SuiteRegistryInterface
     */
    private function getSuiteRegistry()
    {
        return $this->getContainer()->get('sylius_fixtures.suite_registry');
    }

    /**
     * @return SuiteLoaderInterface
     */
    private function getSuiteLoader()
    {
        return $this->getContainer()->get('sylius_fixtures.suite_loader');
    }
}
