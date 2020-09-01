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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Updater\UnpaidOrdersStateUpdater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @final
 */
class CancelUnpaidOrdersCommand extends Command
{
    protected static $defaultName = 'sylius:cancel-unpaid-orders';

    /**
     * @var string
     */
    private $orderExpirationPeriod;

    /**
     * @var UnpaidOrdersStateUpdater
     */
    private $unpaidCartsStateUpdater;

    /**
     * @var ObjectManager
     */
    private $orderManager;

    public function __construct(
        string $orderExpirationPeriod,
        UnpaidOrdersStateUpdater $unpaidCartsStateUpdater,
        ObjectManager $orderManager
    ) {
        parent::__construct();

        $this->orderExpirationPeriod = $orderExpirationPeriod;
        $this->unpaidCartsStateUpdater = $unpaidCartsStateUpdater;
        $this->orderManager = $orderManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(
                'Removes order that have been unpaid for a configured period. Configuration parameter - sylius_order.order_expiration_period.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(sprintf(
            'Command will cancel orders that have been unpaid for <info>%s</info>.',
            $this->orderExpirationPeriod
        ));

        $this->unpaidCartsStateUpdater->cancel();

        $this->orderManager->flush();

        return 0;
    }
}
