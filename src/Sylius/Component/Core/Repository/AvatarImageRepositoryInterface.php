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

namespace Sylius\Component\Core\Repository;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of ImageInterface
 *
 * @extends RepositoryInterface<T>
 */
interface AvatarImageRepositoryInterface extends RepositoryInterface
{
    public function findOneByOwnerId(string $id): ?ImageInterface;
}
