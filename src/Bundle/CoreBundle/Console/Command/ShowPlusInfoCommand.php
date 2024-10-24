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

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'sylius:show-plus-info',
    description: 'Shows information about Sylius Plus and Sylius Store',
)]
final class ShowPlusInfoCommand extends Command
{
    private const SYLIUS_PLUS_URL = 'https://sylius.com/plus/';

    private const SYLIUS_STORE_URL = 'https://store.sylius.com/';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->section('Sylius Plus');
        $io->writeln('<comment>Consists of modules: </comment>');
        $io->listing($this->getPlusModules());
        $io->writeln('<comment>For more information visit: </comment> <href=' . self::SYLIUS_PLUS_URL . '>' . self::SYLIUS_PLUS_URL . '</>');

        $io->section('Sylius Store');
        $io->writeln('<comment>A wide range of community plugins both open source and licensed</comment> <href=' . self::SYLIUS_STORE_URL . '>' . self::SYLIUS_STORE_URL . '</>');

        return Command::SUCCESS;
    }

    /** @return string[] */
    private function getPlusModules(): array
    {
        return [
            '<info>B2B Suite</info>',
            '<info>Marketplace Suite</info>',
            '<info>Advanced Multi-store</info>',
            '<info>Returns Management (RMA)</info>',
            '<info>Multi-source Inventory</info>',
            '<info>Loyalty system</info>',
            '<info>RBAC (Role-Based Access Control)</info>',
            '<info>Partial Shipment</info>',
            '<info>One page checkout</info>',
        ];
    }
}
