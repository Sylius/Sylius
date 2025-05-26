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

namespace Tests\Sylius\Component\User\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\User\Model\UserOAuth;
use Sylius\Component\User\Model\UserOAuthInterface;

final class UserOAuthTest extends TestCase
{
    private UserOAuth $userOAuth;

    protected function setUp(): void
    {
        $this->userOAuth = new UserOAuth();
    }

    public function testShouldImplementUserOauthInterface(): void
    {
        $this->assertInstanceOf(UserOAuthInterface::class, $this->userOAuth);
    }
}
