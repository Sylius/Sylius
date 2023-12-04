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

namespace Sylius\Bundle\ApiBundle\Command\Account;

use Sylius\Bundle\ApiBundle\Command\ShopUserIdAwareInterface;

/** @experimental */
class ChangeShopUserPassword implements ShopUserIdAwareInterface
{
    /** @var mixed|null */
    public $shopUserId;

    /**
     * @immutable
     *
     * @var string|null
     */
    public $newPassword;

    /**
     * @immutable
     *
     * @var string|null
     */
    public $confirmNewPassword;

    /**
     * @immutable
     *
     * @var string|null
     */
    public $currentPassword;

    public function __construct(?string $newPassword, ?string $confirmNewPassword, ?string $currentPassword)
    {
        $this->newPassword = $newPassword;
        $this->confirmNewPassword = $confirmNewPassword;
        $this->currentPassword = $currentPassword;
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
