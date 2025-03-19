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

namespace Sylius\Bundle\PaymentBundle\Console\Command;

use ParagonIE\Halite\Alerts\CannotPerformOperation;
use ParagonIE\Halite\Alerts\InvalidKey;
use ParagonIE\Halite\KeyFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'sylius:payment:generate-key',
    description: 'Generate a key for Sylius payment encryption.',
)]
final class GenerateEncryptionKeyCommand extends Command
{
    protected SymfonyStyle $io;

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly string $keyPath,
    ) {
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->writeln('Generating encryption key for Sylius payment encryption');

        if (false === $input->getOption('overwrite') && $this->filesystem->exists($this->keyPath)) {
            $this->io->writeln(sprintf('Key file "%s" already exists.', $this->keyPath));

            $answer = $this->io->confirm('Do you want to overwrite it?', false);
            if (false === $answer) {
                $this->io->info('Key generation has been canceled');

                return Command::SUCCESS;
            }
        }

        try {
            $generatedKey = KeyFactory::generateEncryptionKey();
        } catch (CannotPerformOperation|InvalidKey|\TypeError) {
            $this->io->error('Key could not be generated. Please, make sure that PHP supports libsodium');

            return Command::FAILURE;
        }

        try {
            $this->filesystem->mkdir(\dirname($this->keyPath));
            $this->filesystem->touch($this->keyPath);
            $saved = KeyFactory::save($generatedKey, $this->keyPath);
        } catch (IOException) {
            $saved = false;
        }

        if (false === $saved) {
            $this->io->error(sprintf(
                'Key could not be saved. Please, make sure that the directory "%s" is writable',
                \dirname($this->keyPath),
            ));

            return Command::FAILURE;
        }

        $this->io->success(sprintf('Key has been generated and saved in "%s"', $this->keyPath));

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addOption('overwrite', null, InputOption::VALUE_NONE, 'Overwrites an existing key file');
    }
}
