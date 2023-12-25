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

namespace Sylius\Bundle\OrderBundle\Command;

use Sylius\Component\Order\Remover\ExpiredCartsRemoverInterface;
use SyliusLabs\Polyfill\Symfony\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/** @final */
class RemoveExpiredCartsCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'sylius:remove-expired-carts';

    public function __construct(
        private ExpiredCartsRemoverInterface $expiredCartsRemover,
        private string $expirationTime,
    ) {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Removes carts that have been idle for a period set in `sylius_order.expiration.cart` configuration key.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf(
            'Command will remove carts that have been idle for <info>%s</info>.',
            $this->expirationTime,
        ));

        $this->expiredCartsRemover->remove();

        return 0;
    }
}
