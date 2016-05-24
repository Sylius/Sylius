<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Uploader;

use Gaufrette\Filesystem;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface PathProviderInterface
{
    /**
     * @param Filesystem $filesystem
     * @param \SplFileInfo $file
     *
     * @return string
     */
    public function provide(Filesystem $filesystem, \SplFileInfo $file);
}
