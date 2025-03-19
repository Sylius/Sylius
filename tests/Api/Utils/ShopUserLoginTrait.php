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

namespace Sylius\Tests\Api\Utils;

/** @deprecated Call $this->setUpShopUserContext() instead. */
trait ShopUserLoginTrait
{
    use UserLoginTrait;

    /** @deprecated Call $this->setUpShopUserContext() instead. */
    protected function logInShopUser(string $email): array
    {
        return $this->logInUser('shop', $email);
    }
}
