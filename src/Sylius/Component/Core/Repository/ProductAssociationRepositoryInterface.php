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

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ProductAssociationRepositoryInterface extends RepositoryInterface
{
    public function findAssociatedProductsWithinChannel($associationId, $productId, ChannelInterface $channel): ProductAssociationInterface;
}
