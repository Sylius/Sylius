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

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of ProductAssociationInterface
 *
 * @extends RepositoryInterface<T>
 */
interface ProductAssociationRepositoryInterface extends RepositoryInterface
{
    public function findWithProductsWithinChannel(int $associationId, ChannelInterface $channel): ProductAssociationInterface;
}
