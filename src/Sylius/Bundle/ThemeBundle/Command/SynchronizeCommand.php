<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Command;

use Sylius\Bundle\ThemeBundle\Synchronizer\ThemeSynchronizerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SynchronizeCommand extends Command
{
    /**
     * @var ThemeSynchronizerInterface
     */
    private $themeSynchronizer;

    /**
     * @param ThemeSynchronizerInterface $themeSynchronizer
     */
    public function __construct(ThemeSynchronizerInterface $themeSynchronizer)
    {
        parent::__construct();

        $this->themeSynchronizer = $themeSynchronizer;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:theme:synchronize')
            ->setDescription('Synchronize themes.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Synchronizing themes... ');

        $this->themeSynchronizer->synchronize();

        $output->writeln('Success!');
    }
}
