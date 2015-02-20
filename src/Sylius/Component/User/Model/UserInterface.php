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

use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\UserInterface as BaseUserInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * User interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface UserInterface extends BaseUserInterface, TimestampableInterface
{
    /**
     * Get first name.
     *
     * @return string
     */
    public function getFirstName();

    /**
     * Set first name
     *
     * @param string $firstName
     */
    public function setFirstName($firstName);

    /**
     * Get last name.
     *
     * @return string
     */
    public function getLastName();

    /**
     * Set last name.
     *
     * @param string $lastName
     */
    public function setLastName($lastName);

    /**
     * Get connected OAuth accounts.
     *
     * @return Collection|UserOAuthInterface[]
     */
    public function getOAuthAccounts();

    /**
     * Get connected OAuth account.
     *
     * @param string $provider
     *
     * @return null|UserOAuthInterface
     */
    public function getOAuthAccount($provider);

    /**
     * Connect OAuth account.
     *
     * @param UserOAuthInterface $oauth
     *
     * @return self
     */
    public function addOAuthAccount(UserOAuthInterface $oauth);
    
    /**
     * Check whether user is deleted.
     * 
     * @return bool
     */
    public function isDeleted();
}
