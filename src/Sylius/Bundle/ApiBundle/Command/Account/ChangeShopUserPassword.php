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

class ChangeShopUserPassword implements ShopUserIdAwareInterface
{
    public function __construct(
        protected mixed $shopUserId,
        protected string $newPassword,
        protected string $confirmNewPassword,
        protected string $currentPassword,
    ) {
    }

    public function getShopUserId()
    {
        return $this->shopUserId;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    public function getConfirmNewPassword(): string
    {
        return $this->confirmNewPassword;
    }

    public function getCurrentPassword(): string
    {
        return $this->currentPassword;
    }
}
