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
 * User model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class UserOauth
{

    protected $id;
    protected $provider;
    protected $canonicalId;
    protected $user;




    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set provider
     *
     * @param string $provider
     *
     * @return UserOauth
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set canonicalId
     *
     * @param string $canonicalId
     *
     * @return UserOauth
     */
    public function setCanonicalId($canonicalId)
    {
        $this->canonicalId = $canonicalId;

        return $this;
    }

    /**
     * Get canonicalId
     *
     * @return string
     */
    public function getCanonicalId()
    {
        return $this->canonicalId;
    }




    /**
     * Set user
     *
     * @param UserInterface $user
     *
     * @return UserOauth
     */
    public function setUser(UserInterface $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
}
