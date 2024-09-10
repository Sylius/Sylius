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

use Sylius\Bundle\ApiBundle\Attribute\ShopUserIdAware;

#[ShopUserIdAware]
class ChangeShopUserPassword
{
    public function __construct(
        public readonly string $newPassword,
        public readonly string $confirmNewPassword,
        public readonly string $currentPassword,
        public readonly mixed $shopUserId,
    ) {
    }
}
