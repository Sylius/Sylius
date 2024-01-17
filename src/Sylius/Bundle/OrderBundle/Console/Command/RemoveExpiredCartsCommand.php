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
use SyliusLabs\Polyfill\Symfony\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @final
 */
class RemoveExpiredCartsCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'sylius:remove-expired-carts';

    public function __construct(
        private ?ExpiredCartsRemoverInterface $expiredCartsRemover = null,
        private ?string $expirationTime = null,
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
        if ($this->expirationTime === null) {
            trigger_deprecation(
                'sylius/order-bundle',
                '1.12',
                'Not injecting the expiration time into %s is deprecated and will be mandatory from Sylius 2.0',
                self::class,
            );
            $expirationTime = $this->getContainer()->getParameter('sylius_order.cart_expiration_period');
        } else {
            $expirationTime = $this->expirationTime;
        }

        $output->writeln(sprintf(
            'Command will remove carts that have been idle for <info>%s</info>.',
            (string) $expirationTime,
        ));

        if ($this->expiredCartsRemover === null) {
            trigger_deprecation(
                'sylius/order-bundle',
                '1.12',
                'Not injecting the %s into the %s is deprecated and will be mandatory from Sylius 2.0',
                ExpiredCartsRemoverInterface::class,
                self::class,
            );
            $this->getContainer()->get('sylius.expired_carts_remover')->remove();
        } else {
            $this->expiredCartsRemover->remove();
        }

        return 0;
    }
}

class_alias(RemoveExpiredCartsCommand::class, \Sylius\Bundle\OrderBundle\Command\RemoveExpiredCartsCommand::class);
