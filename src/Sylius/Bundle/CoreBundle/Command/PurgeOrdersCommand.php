<?php

namespace Sylius\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to purge expired pending orders
 *
 * @author Ka-Yue Yeung <kayuey@gmail.com>
 */
class PurgeOrdersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sylius:order:purge')
            ->setDescription('Purge expired pending orders')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Purging expired pending orders...');

        $cartsPurger = $this->getContainer()->get('sylius.order.purger');
        $cartsPurger->purge();

        $output->writeln('Expired pending orders purged.');
    }
}
