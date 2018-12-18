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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InformAboutGUSCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('sylius:inform-about-gus');
        $this->setDescription('Informs about Sylius internal statistical service');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $style = new SymfonyStyle($input, $output);

        $style->note(
            [
                'In order to inform you about newest Sylius releases and be aware of shops based on Sylius, the Core Team uses an internal statistical service called GUS.',
                'The only data that is collected and stored in its database are hostname, user agent, locale, environment (test, dev or prod), current Sylius version and the date of last contact.',
                'If you do not want your shop to send requests to GUS, please visit:',
                'https://docs.sylius.com/en/1.2/cookbook/configuration/disabling-admin-notifications.html',
                'for further instructions',
            ]
        );
    }
}
