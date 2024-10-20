<?php

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Console\Command;

use ParagonIE\ConstantTime\Hex;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'sylius:payment:generate-salt',
    description: 'Generate encryption salt for Sylius payment encryption.',
)]
final class GenerateEncryptionSaltCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Generating encryption salt for Sylius payment encryption');

        try {
            $output->writeln('Salt: ' . Hex::encode(random_bytes(\SODIUM_CRYPTO_PWHASH_SALTBYTES)));
        } catch (\TypeError) {
            $output->writeln('Salt could not be generated. Please, make sure that PHP is compiled with libsodium support');

            return Command::FAILURE;
        }

        $output->writeln('Please, remember to update your configuration with this salt');

        return Command::SUCCESS;
    }
}
