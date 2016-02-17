<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\User\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Sergio Marchesini
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
interface UserOAuthInterface extends UserAwareInterface, ResourceInterface
{
    /**
     * Get OAuth provider name.
     *
     * @return string
     */
    public function getProvider();

    /**
     * Set OAuth provider name.
     *
     * @param string $provider
     */
    public function setProvider($provider);

    /**
     * Get OAuth identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Set OAuth identifier.
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier);

    /**
     * Get OAuth access token.
     *
     * @return string
     */
    public function getAccessToken();

    /**
     * Set OAuth access token.
     *
     * @param string $accessToken
     */
    public function setAccessToken($accessToken);
}
