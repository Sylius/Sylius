<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Updater\UnpaidOrdersStateUpdaterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * @final
 */
class CancelUnpaidOrdersCommand extends Command
{
    use ContainerAwareTrait;

    protected static $defaultName = 'sylius:cancel-unpaid-orders';

    protected function configure(): void
    {
        $this
            ->setDescription(
                'Removes order that have been unpaid for a configured period. Configuration parameter - sylius_order.order_expiration_period.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $expirationTime = $this->container->getParameter('sylius_order.order_expiration_period');
        $output->writeln(sprintf(
            'Command will cancel orders that have been unpaid for <info>%s</info>.',
            (string) $expirationTime
        ));

        /** @var UnpaidOrdersStateUpdaterInterface $unpaidCartsStateUpdater */
        $unpaidCartsStateUpdater = $this->container->get('sylius.unpaid_orders_state_updater');
        $unpaidCartsStateUpdater->cancel();

        /** @var EntityManagerInterface $orderManager */
        $orderManager = $this->container->get('sylius.manager.order');
        $orderManager->flush();

        return 0;
    }
}
