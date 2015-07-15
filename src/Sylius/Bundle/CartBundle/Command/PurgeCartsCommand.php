<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to purge expired carts
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class PurgeCartsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sylius:cart:purge')
            ->setDescription('Purge expired carts')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Purging expired carts...');

        $cartsPurger = $this->getContainer()->get('sylius.cart.purger');
        $cartsPurger->purge();

        $output->writeln('Expired carts purged.');
    }
}
