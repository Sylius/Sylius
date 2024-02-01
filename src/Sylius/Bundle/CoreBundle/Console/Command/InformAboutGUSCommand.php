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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InformAboutGUSCommand extends Command
{
    protected static $defaultName = 'sylius:inform-about-gus';

    protected function configure(): void
    {
        $this->setDescription('Informs about Sylius internal statistical service');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $style->note(
            [
                'For purely statistical purposes and in order to inform you about important updates and security patches, Sylius might send non-sensitive data to our servers. We send:',
                '* Hostname',
                '* User-agent',
                '* Locale',
                '* Environment (test, dev or prod)',
                '* Currently used Sylius version',
                '* Date of the last contact',
                'If you do not consent please follow this cookbook article:',
                'https://docs.sylius.com/en/latest/cookbook/configuration/disabling-admin-notifications.html',
                'That being said, every time we get a notification about a new site deployed with Sylius, it brings a huge smile to our faces and motivates us to continue our Open Source work.',
            ],
        );

        return 0;
    }
}
