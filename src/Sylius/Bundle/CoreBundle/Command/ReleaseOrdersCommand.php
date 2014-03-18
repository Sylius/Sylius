<?php

namespace Sylius\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to release expired pending orders
 *
 * @author Ka-Yue Yeung <kayuey@gmail.com>
 */
class ReleaseOrdersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sylius:order:release')
            ->setDescription('Release expired pending orders')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Release expired pending orders...');

        $orderReleaser = $this->getContainer()->get('sylius.order.releaser');
        $orderReleaser->release();

        $output->writeln('Expired pending orders released.');
    }
}
