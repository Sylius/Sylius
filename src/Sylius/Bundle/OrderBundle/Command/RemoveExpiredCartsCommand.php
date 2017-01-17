<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class RemoveExpiredCartsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:remove-expired-carts')
            ->setDescription('Removes carts that have been idle for a configured period. Configuration parameter - sylius_order.cart_expires_after.');
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $expirationTime = $this->getContainer()->getParameter('sylius_order.cart_expiration_period');
        $output->writeln(
            sprintf('Command will remove carts that have been idle for <info>%s</info>.', $expirationTime)
        );

        $expiredCartsRemover = $this->getContainer()->get('sylius.expired_carts_remover');
        $expiredCartsRemover->remove();

        $this->getContainer()->get('sylius.manager.order')->flush();
    }
}
