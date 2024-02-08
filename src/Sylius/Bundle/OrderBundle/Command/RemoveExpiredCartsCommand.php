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

use SyliusLabs\Polyfill\Symfony\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @final
 */
class RemoveExpiredCartsCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'sylius:remove-expired-carts';

    protected function configure(): void
    {
        $this
            ->setDescription('Removes carts that have been idle for a period set in `sylius_order.expiration.cart` configuration key.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $expirationTime = $this->getContainer()->getParameter('sylius_order.cart_expiration_period');
        $output->writeln(sprintf(
            'Command will remove carts that have been idle for <info>%s</info>.',
            (string) $expirationTime,
        ));

        $expiredCartsRemover = $this->getContainer()->get('sylius.expired_carts_remover');
        $expiredCartsRemover->remove();

        return 0;
    }
}
