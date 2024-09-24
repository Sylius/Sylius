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
use Sylius\Bundle\CoreBundle\Installer\Checker\RequirementsCheckerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'sylius:install:check-requirements',
    description: 'Checks if all Sylius requirements are satisfied.',
)]
final class CheckRequirementsCommand extends Command
{
    public function __construct(
        private readonly RequirementsCheckerInterface $requirementsChecker,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command checks system requirements.
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fulfilled = $this->requirementsChecker->check($input, $output);

        if (!$fulfilled) {
            throw new RuntimeException(
                'Some system requirements are not fulfilled. Please check output messages and fix them.',
            );
        }

        $output->writeln('<info>Success! Your system can run Sylius properly.</info>');

        return Command::SUCCESS;
    }
}
