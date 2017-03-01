<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CancelUnpaidOrdersCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:cancel-unpaid-orders')
            ->setDescription(
                'Removes order that have been unpaid for a configured period. Configuration parameter - sylius_order.order_expiration_period.'
            );
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $expirationTime = $this->getContainer()->getParameter('sylius_order.order_expiration_period');

        $output->writeln(sprintf(
            'Command will cancel orders that have been unpaid for <info>%s</info>.',
            $expirationTime
        ));

        $unpaidCartsStateUpdater = $this->getContainer()->get('sylius.unpaid_orders_state_updater');
        $unpaidCartsStateUpdater->cancel();

        $this->getContainer()->get('sylius.manager.order')->flush();
    }
}
