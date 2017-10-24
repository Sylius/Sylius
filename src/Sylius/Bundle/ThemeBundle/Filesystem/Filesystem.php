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

namespace Sylius\Bundle\ThemeBundle\Filesystem;

use Symfony\Component\Filesystem\Filesystem as BaseFilesystem;

final class Filesystem extends BaseFilesystem implements FilesystemInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFileContents(string $file): string
    {
        return file_get_contents($file);
    }
}
