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

use Sylius\Bundle\OrderBundle\Remover\ExpiredCartsRemover;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @final
 */
class RemoveExpiredCartsCommand extends Command
{
    protected static $defaultName = 'sylius:remove-expired-carts';

    /**
     * @var string
     */
    private $orderExpirationPeriod;

    /**
     * @var ExpiredCartsRemover
     */
    private $expiredCartsRemover;

    public function __construct(
        string $orderExpirationPeriod,
        ExpiredCartsRemover $expiredCartsRemover
    ) {
        parent::__construct();

        $this->orderExpirationPeriod = $orderExpirationPeriod;
        $this->expiredCartsRemover = $expiredCartsRemover;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Removes carts that have been idle for a period set in `sylius_order.expiration.cart` configuration key.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(
            sprintf('Command will remove carts that have been idle for <info>%s</info>.', $this->orderExpirationPeriod)
        );

        $this->expiredCartsRemover->remove();

        return 0;
    }
}
