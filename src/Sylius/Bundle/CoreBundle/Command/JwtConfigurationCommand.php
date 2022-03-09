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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

final class JwtConfigurationCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'sylius:install:jwt-setup';

    protected function configure(): void
    {
        $this
            ->setDescription('Setup JWT token')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command generates JWT token.
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $output->writeln('Generating JWT token for Sylius API');

        $question = new ConfirmationQuestion('Do you want to generate JWT token? (y/N)', false);

        if (!$helper->ask($input, $output, $question)) {
            return 0;
        }

        $this->commandExecutor->runCommand('lexik:jwt:generate-keypair', ['--overwrite' => true], $output);

        $output->writeln('Please, remember to enable Sylius API');
        $output->writeln('https://docs.sylius.com/en/1.10/book/api/introduction.html');

        return 0;
    }
}
