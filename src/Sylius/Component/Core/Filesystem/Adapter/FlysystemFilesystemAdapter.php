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

namespace Sylius\Component\Core\Filesystem\Adapter;

use League\Flysystem\FilesystemOperator;

class FlysystemFilesystemAdapter implements FilesystemAdapterInterface
{
    public function __construct(private FilesystemOperator $filesystem)
    {
    }

    public function has(string $location): bool
    {
        return $this->filesystem->fileExists($location);
    }

    public function write(string $location, string $content): void
    {
        $this->filesystem->write($location, $content);
    }

    public function delete(string $location): void
    {
        if (!$this->has($location)) {
            throw new \InvalidArgumentException(sprintf('Cannot delete nonexistent file "%s".', $location));
        }

        $this->filesystem->delete($location);
    }
}
