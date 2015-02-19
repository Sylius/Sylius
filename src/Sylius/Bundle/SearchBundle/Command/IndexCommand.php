<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to populate a search index
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class IndexCommand extends ContainerAwareCommand
{
    /**
     * @see Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('sylius:search:index')
            ->setDescription('Populate the index')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Index populate command');

        $indexer = $this->getContainer()
            ->get('sylius_search.command.indexer')
            ->populate($this->getContainer()->get('doctrine.orm.entity_manager'))
        ;

        $output->writeln($indexer->getOutput());
    }
}
