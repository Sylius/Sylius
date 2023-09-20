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

namespace Sylius\Component\Core\Filesystem\Adapter;

use Gaufrette\FilesystemInterface;
use Sylius\Component\Core\Filesystem\Exception\FileNotFoundException;

/**
 * @deprecated since version 1.12, to be removed in 2.0. Use {@link FlysystemFilesystemAdapter} instead.
 */
final class GaufretteFilesystemAdapter implements FilesystemAdapterInterface
{
    public function __construct(private FilesystemInterface $filesystem)
    {
        trigger_deprecation(
            'sylius/core',
            '1.12',
            'The "%s" class is deprecated and will be removed in 2.0. Use "%s" instead.',
            self::class,
            FlysystemFilesystemAdapter::class,
        );
    }

    public function has(string $location): bool
    {
        return $this->filesystem->has($location);
    }

    public function write(string $location, string $content): void
    {
        $this->filesystem->write($location, $content);
    }

    public function delete(string $location): void
    {
        if (!$this->has($location)) {
            throw new FileNotFoundException($location);
        }

        $this->filesystem->delete($location);
    }
}
