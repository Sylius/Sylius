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

namespace Sylius\Component\User\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface UserOAuthInterface extends UserAwareInterface, ResourceInterface
{
    public function getProvider(): ?string;

    public function setProvider(?string $provider): void;

    public function getIdentifier(): ?string;

    public function setIdentifier(?string $identifier): void;

    public function getAccessToken(): ?string;

    public function setAccessToken(?string $accessToken): void;

    public function getRefreshToken(): ?string;

    public function setRefreshToken(?string $refreshToken): void;
}
