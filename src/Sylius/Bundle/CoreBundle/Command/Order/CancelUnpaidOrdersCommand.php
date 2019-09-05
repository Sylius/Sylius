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

namespace Sylius\Bundle\CoreBundle\Command\Order;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Updater\UnpaidOrdersStateUpdaterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CancelUnpaidOrdersCommand extends Command
{
    /** @var string */
    private $orderExpirationPeriod;

    /** @var UnpaidOrdersStateUpdaterInterface */
    private $unpaidOrdersStateUpdater;

    /** @var ObjectManager */
    private $orderManager;

    public function __construct(
        string $orderExpirationPeriod,
        UnpaidOrdersStateUpdaterInterface $unpaidOrdersStateUpdater,
        ObjectManager $orderManager
    ) {
        $this->orderExpirationPeriod = $orderExpirationPeriod;
        $this->unpaidOrdersStateUpdater = $unpaidOrdersStateUpdater;
        $this->orderManager = $orderManager;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sylius:cancel-unpaid-orders')
            ->setDescription(
                'Removes order that have been unpaid for a configured period. Configuration parameter - sylius_order.order_expiration_period.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln(sprintf(
            'Command will cancel orders that have been unpaid for <info>%s</info>.',
            $this->orderExpirationPeriod
        ));

        $this->unpaidOrdersStateUpdater->cancel();

        $this->orderManager->flush();
    }
}
