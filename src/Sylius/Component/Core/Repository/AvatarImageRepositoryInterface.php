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

namespace Sylius\Component\Core\Repository;

use Sylius\Component\Core\Model\AvatarImage;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface AvatarImageRepositoryInterface extends RepositoryInterface
{
    public function findOneByOwnerId(string $id): ?ImageInterface;
}
