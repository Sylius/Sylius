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

namespace Sylius\Bundle\ApiBundle\Uploader;

use Sylius\Component\Core\Model\ImageInterface;

/** @experimental */
interface ImageUploaderInterface
{
    public function create(string $ownerCode, ?\SplFileInfo $file, ?string $type): ImageInterface;

    public function modify(string $ownerCode, string $imageId, ?\SplFileInfo $file, ?string $type): ImageInterface;
}
