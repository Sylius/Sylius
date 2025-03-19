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

use Sylius\Bundle\CoreBundle\Installer\Executor\CommandExecutor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Webmozart\Assert\Assert;

#[AsCommand(
    name: 'sylius:install:jwt-setup',
    description: 'Setup JWT token.',
)]
final class JwtConfigurationCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command generates JWT token.
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var HelperInterface $helper */
        $helper = $this->getHelper('question');

        $output->writeln('Generating JWT token for Sylius API');

        $question = new ConfirmationQuestion('Do you want to generate JWT token? (y/N)', false);

        Assert::isInstanceOf($helper, QuestionHelper::class);
        if (!$helper->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        $commandExecutor = new CommandExecutor($input, $output, $this->getApplication());
        $commandExecutor->runCommand('lexik:jwt:generate-keypair', ['--overwrite' => true], $output);

        $output->writeln('Please, remember to enable Sylius API');
        $output->writeln('https://docs.sylius.com/en/1.10/book/api/introduction.html');

        return Command::SUCCESS;
    }
}
