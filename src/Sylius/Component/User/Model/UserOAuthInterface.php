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
     * @return string
     */
    public function getProvider();

    /**
     * @param string $provider
     */
    public function setProvider($provider);

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier);

    /**
     * @return string
     */
    public function getAccessToken();

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken);

    /**
     * @return string
     */
   public function getRefreshToken();

   /**
    * @param string $refreshToken
    */
   public function setRefreshToken($refreshToken);
}
