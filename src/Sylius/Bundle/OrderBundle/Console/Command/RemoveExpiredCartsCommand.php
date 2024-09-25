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

namespace Sylius\Bundle\OrderBundle\Console\Command;

use Sylius\Component\Order\Remover\ExpiredCartsRemoverInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @final
 */
#[AsCommand(
    name: 'sylius:remove-expired-carts',
    description: 'Removes carts that have been idle for a period set in `sylius_order.expiration.cart` configuration key.',
)]
class RemoveExpiredCartsCommand extends Command
{
    public function __construct(
        private ExpiredCartsRemoverInterface $expiredCartsRemover,
        private string $expirationTime,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(sprintf(
            'Command will remove carts that have been idle for <info>%s</info>.',
            $this->expirationTime,
        ));

        $this->expiredCartsRemover->remove();

        return Command::SUCCESS;
    }
}
