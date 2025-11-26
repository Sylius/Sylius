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

/** @deprecated Call $this->setUpAdminContext() instead. */
trait AdminUserLoginTrait
{
    use UserLoginTrait;

    /** @deprecated Call $this->setUpAdminContext() instead. */
    protected function logInAdminUser(string $email): array
    {
        return $this->logInUser('admin', $email);
    }
}
