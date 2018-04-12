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

namespace Sylius\Component\Core\Uploader;

use Sylius\Component\Core\Model\FileInterface;

interface FileUploaderInterface
{
    /**
     * @param FileInterface $image
     */
    public function upload(FileInterface $file): void;

    /**
     * @param string $path
     *
     * @return bool
     */
    public function remove(string $path): bool;
}
