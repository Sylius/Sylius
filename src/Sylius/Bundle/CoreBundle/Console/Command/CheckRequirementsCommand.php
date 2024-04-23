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

use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckRequirementsCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'sylius:install:check-requirements';

    protected function configure(): void
    {
        $this
            ->setDescription('Checks if all Sylius requirements are satisfied.')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command checks system requirements.
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fulfilled = $this->getContainer()->get('sylius.installer.checker.sylius_requirements')->check($input, $output);

        if (!$fulfilled) {
            throw new RuntimeException(
                'Some system requirements are not fulfilled. Please check output messages and fix them.',
            );
        }

        $output->writeln('<info>Success! Your system can run Sylius properly.</info>');

        return 0;
    }
}

class_alias(CheckRequirementsCommand::class, '\Sylius\Bundle\CoreBundle\Command\CheckRequirementsCommand');
