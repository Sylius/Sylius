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

use Sylius\Bundle\CoreBundle\Form\Type\ImageType;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'sylius:cleanup-cached-uploads',
    description: 'Removes old cached upload files from temporary storage.',
)]
final class CleanupCachedUploadsCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption(
                'max-age',
                null,
                InputOption::VALUE_OPTIONAL,
                'Maximum age of cached files in seconds',
                86400,
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $maxAge = (int) $input->getOption('max-age');
        $cachedUploadsDir = ImageType::getCachedUploadsDirPath();

        if (!is_dir($cachedUploadsDir)) {
            $output->writeln('<info>No cached uploads directory found. Nothing to clean up.</info>');

            return Command::SUCCESS;
        }

        $now = time();
        $deletedCount = 0;
        $failedCount = 0;

        $files = new \FilesystemIterator($cachedUploadsDir);

        foreach ($files as $file) {
            if (!$file instanceof \SplFileInfo || !$file->isFile() || ($now - $file->getMTime()) <= $maxAge) {
                continue;
            }

            if (@unlink($file->getPathname())) {
                ++$deletedCount;
            } else {
                ++$failedCount;
                $output->writeln(sprintf('<comment>Could not delete: %s</comment>', $file->getPathname()));
            }
        }

        $output->writeln(sprintf(
            '<info>Cleanup complete. Removed %d cached file(s) older than %d seconds.</info>',
            $deletedCount,
            $maxAge,
        ));

        return $failedCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
