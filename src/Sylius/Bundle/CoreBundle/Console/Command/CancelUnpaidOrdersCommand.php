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

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use SyliusLabs\Polyfill\Symfony\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CancelUnpaidOrdersCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'sylius:cancel-unpaid-orders';

    public function __construct(
        private ChannelRepositoryInterface $channelRepository
    ) {}

    protected function configure(): void
    {
        $this
            ->setDescription(
                'Removes order that have been unpaid for a configured period. Configuration parameter - sylius_order.order_expiration_period.',
            )
            ->addArgument(
                'channel',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'Channel filter (optional), supply channel codes'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $channelInput = $input->getArgument('channel', []);
        $channels = !empty($channelInput)
            ? $this->channelRepository->findBy(['code' => $channelInput])
            : $this->channelRepository->findAll();

        $expirationTime = $this->getContainer()->getParameter('sylius_order.order_expiration_period');
        $output->writeln(sprintf(
            'Command will cancel orders that have been unpaid for <info>%s</info>, channels <info>%s</info>.',
            (string) $expirationTime,
            join(', ', array_map(fn (ChannelInterface $channel) => $channel->getCode(), $channels)),
        ));

        $unpaidCartsStateUpdater = $this->getContainer()->get('sylius.unpaid_orders_state_updater');
        foreach ($channels as $channel) {
            $unpaidCartsStateUpdater->cancel($channel);

            $this->getContainer()->get('sylius.manager.order')->flush();
        }

        $output->writeln('<info>Unpaid orders have been canceled</info>');

        return 0;
    }
}

class_alias(CancelUnpaidOrdersCommand::class, '\Sylius\Bundle\CoreBundle\Command\CancelUnpaidOrdersCommand');
