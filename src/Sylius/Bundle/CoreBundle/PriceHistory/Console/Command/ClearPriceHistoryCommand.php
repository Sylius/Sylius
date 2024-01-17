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

namespace Sylius\Bundle\CoreBundle\PriceHistory\Console\Command;

use Sylius\Bundle\CoreBundle\PriceHistory\Remover\ChannelPricingLogEntriesRemoverInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'sylius:price-history:clear',
    description: 'Clears the price history up to a given number of days ago',
)]
final class ClearPriceHistoryCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(private ChannelPricingLogEntriesRemoverInterface $channelPricingLogEntriesRemover)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('days', InputArgument::REQUIRED, 'Number of days ago');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $days = filter_var($input->getArgument('days'), \FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (false === $days) {
            $this->io->error('Number of days must be an integer greater than 0');

            return Command::FAILURE;
        }

        if ($input->isInteractive()) {
            $confirmation = $this->io->confirm(sprintf(
                'Are you sure you want to clear the price history from before %s days ago?',
                $days,
            ), false);

            if (false === $confirmation) {
                return Command::INVALID;
            }
        }

        $this->channelPricingLogEntriesRemover->remove($days);

        return Command::SUCCESS;
    }
}
