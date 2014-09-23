<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Component\Core\Model;

/**
 * User OAuth account interface.
 *
 * @author Sergio Marchesini
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
interface UserOAuthInterface extends UserAwareInterface
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
     *
     * @return self
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
     *
     * @return self
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
     *
     * @return self
     */
    public function setAccessToken($accessToken);
}
