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

namespace Sylius\Bundle\OrderBundle\Command;

use Sylius\Component\Order\Remover\ExpiredCartsRemoverInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * @final
 */
class RemoveExpiredCartsCommand extends Command
{
    use ContainerAwareTrait;

    protected static $defaultName = 'sylius:remove-expired-carts';

    protected function configure(): void
    {
        $this
            ->setDescription('Removes carts that have been idle for a period set in `sylius_order.expiration.cart` configuration key.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $expirationTime = $this->container->getParameter('sylius_order.cart_expiration_period');
        $output->writeln(sprintf(
            'Command will remove carts that have been idle for <info>%s</info>.',
            (string) $expirationTime
        ));

        /** @var ExpiredCartsRemoverInterface $expiredCartsRemover */
        $expiredCartsRemover = $this->container->get('sylius.expired_carts_remover');
        $expiredCartsRemover->remove();

        return 0;
    }
}
