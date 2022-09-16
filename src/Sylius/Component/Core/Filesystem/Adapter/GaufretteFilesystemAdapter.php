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

use Gaufrette\FilesystemInterface;
use Sylius\Component\Core\Filesystem\Exception\FileNotFoundException;

/**
 * @deprecated since version 1.12, to be removed in 2.0. Use {@link FilesystemInterface} instead.
 */
class GaufretteFilesystemAdapter implements FilesystemAdapterInterface
{
    public function __construct(private FilesystemInterface $filesystem)
    {
        @trigger_error(sprintf(
            'The "%s" class is deprecated since Sylius 1.12 and will be removed in 2.0. Use "%s" instead.',
            GaufretteFilesystemAdapter::class,
            FlysystemFilesystemAdapter::class,
        ), \E_USER_DEPRECATED);
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
