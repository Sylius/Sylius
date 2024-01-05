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

namespace Sylius\Bundle\ApiBundle\Command\Customer;

use Sylius\Bundle\ApiBundle\Command\ShopUserIdAwareInterface;

/** @experimental */
class RemoveShopUser implements ShopUserIdAwareInterface
{
    public function __construct(private $shopUserId)
    {
    }
    public function getShopUserId()
    {
        return $this->shopUserId;
    }

    public function setShopUserId($shopUserId): void
    {
        $this->shopUserId = $shopUserId;
    }
}
