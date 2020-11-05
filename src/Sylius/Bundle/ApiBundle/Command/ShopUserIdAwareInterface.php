<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Command;

/** @experimental */
interface ShopUserIdAwareInterface extends CommandAwareDataTransformerInterface
{
    public function getShopUserId();

    public function setShopUserId($shopUserId): void;
}
