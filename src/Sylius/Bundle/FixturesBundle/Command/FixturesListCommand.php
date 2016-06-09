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

use Sylius\Bundle\FixturesBundle\Fixture\FixtureRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FixturesListCommand extends Command
{
    /**
     * @var FixtureRegistryInterface
     */
    private $fixtureRegistry;

    /**
     * @param FixtureRegistryInterface $fixtureRegistry
     */
    public function __construct(FixtureRegistryInterface $fixtureRegistry)
    {
        parent::__construct('sylius:fixtures:list');

        $this->fixtureRegistry = $fixtureRegistry;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Lists available fixtures')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fixtures = $this->fixtureRegistry->getFixtures();

        $output->writeln('Available fixtures:');

        foreach ($fixtures as $name => $fixture) {
            $output->writeln(' - ' . $name);
        }
    }
}
