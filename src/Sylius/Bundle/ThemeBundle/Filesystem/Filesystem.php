<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Filesystem;

use Symfony\Component\Filesystem\Filesystem as BaseFilesystem;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class Filesystem extends BaseFilesystem implements FilesystemInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFileContents($file)
    {
        return file_get_contents($file);
    }
}
