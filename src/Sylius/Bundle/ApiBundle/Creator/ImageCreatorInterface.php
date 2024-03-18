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

namespace Sylius\Bundle\ApiBundle\Creator;

use Sylius\Component\Core\Model\ImageInterface;

interface ImageCreatorInterface
{
    /** @param array<mixed> $context */
    public function create(string $ownerCode, ?\SplFileInfo $file, ?string $type, array $context = []): ImageInterface;
}
