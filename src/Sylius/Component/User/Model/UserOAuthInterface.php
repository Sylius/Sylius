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

namespace Sylius\Component\User\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface UserOAuthInterface extends UserAwareInterface, ResourceInterface
{
    /**
     * @return string|null
     */
    public function getProvider(): ?string;

    /**
     * @param string|null $provider
     */
    public function setProvider(?string $provider): void;

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string;

    /**
     * @param string|null $identifier
     */
    public function setIdentifier(?string $identifier): void;

    /**
     * @return string|null
     */
    public function getAccessToken(): ?string;

    /**
     * @param string|null $accessToken
     */
    public function setAccessToken(?string $accessToken): void;

    /**
     * @return string|null
     */
    public function getRefreshToken(): ?string;

    /**
     * @param string|null $refreshToken
     */
    public function setRefreshToken(?string $refreshToken): void;
}
