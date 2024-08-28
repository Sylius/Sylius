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
    name: 'sylius:inform-about-gus',
    description: 'Informs about Sylius internal statistical service.',
)]
final class InformAboutGUSCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $style->info('To inform you about important updates and security patches, Sylius might send non-sensitive data(hostname, user-agent, locale, environment [prod/dev/test]), Sylius version, date of last contact) to our servers. An instruction on how to withdraw consent to this data collection is available in the Sylius documentation.');

        return Command::SUCCESS;
    }
}
