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

namespace Sylius\Component\Core\Uploader;

use Sylius\Component\Core\Model\ImageInterface;

interface ImageUploaderInterface
{
    public function upload(ImageInterface $image): void;

    public function remove(string $path): bool;
}
