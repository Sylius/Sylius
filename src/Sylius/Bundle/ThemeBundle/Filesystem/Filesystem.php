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
class Filesystem extends BaseFilesystem implements FilesystemInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFileInfo($file)
    {
        return new \SplFileInfo($file);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileObject($file)
    {
        return new \SplFileObject($file);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileContents($file)
    {
        $openedFile = $this->getFileObject($file);

        return $openedFile->fread($openedFile->getSize());
    }
}
