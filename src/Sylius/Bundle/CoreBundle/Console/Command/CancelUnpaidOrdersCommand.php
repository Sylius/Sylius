<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Console\Command;

use SyliusLabs\Polyfill\Symfony\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CancelUnpaidOrdersCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'sylius:cancel-unpaid-orders';

    protected function configure(): void
    {
        $this
            ->setDescription(
                'Removes order that have been unpaid for a configured period. Configuration parameter - sylius_order.order_expiration_period.',
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $expirationTime = $this->getContainer()->getParameter('sylius_order.order_expiration_period');
        $output->writeln(sprintf(
            'Command will cancel orders that have been unpaid for <info>%s</info>.',
            (string) $expirationTime,
        ));

        $unpaidCartsStateUpdater = $this->getContainer()->get('sylius.unpaid_orders_state_updater');
        $unpaidCartsStateUpdater->cancel();

        $this->getContainer()->get('sylius.manager.order')->flush();

        $output->writeln('<info>Unpaid orders have been canceled</info>');

        return 0;
    }
}

class_alias(CancelUnpaidOrdersCommand::class, '\Sylius\Bundle\CoreBundle\Command\CancelUnpaidOrdersCommand');
