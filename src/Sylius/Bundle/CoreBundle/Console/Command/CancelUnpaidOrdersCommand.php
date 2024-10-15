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

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Updater\UnpaidOrdersStateUpdaterInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'sylius:cancel-unpaid-orders',
    description: 'Removes order that have been unpaid for a configured period. Configuration parameter - sylius_order.order_expiration_period.',
)]
final class CancelUnpaidOrdersCommand extends Command
{
    public function __construct(
        private readonly UnpaidOrdersStateUpdaterInterface $unpaidOrdersStateUpdater,
        private readonly EntityManagerInterface $orderManager,
        private readonly string $orderExpirationPeriod,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(sprintf(
            'Command will cancel orders that have been unpaid for <info>%s</info>.',
            $this->orderExpirationPeriod,
        ));

        $this->unpaidOrdersStateUpdater->cancel();

        $this->orderManager->flush();

        $output->writeln('<info>Unpaid orders have been canceled</info>');

        return Command::SUCCESS;
    }
}
