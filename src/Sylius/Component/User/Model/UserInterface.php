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
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface UserInterface extends
    AdvancedUserInterface,
    CredentialsHolderInterface,
    ResourceInterface,
    \Serializable,
    TimestampableInterface,
    ToggleableInterface
{
    const DEFAULT_ROLE = 'ROLE_USER';

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     */
    public function setEmail($email);

    /**
     * Gets normalized email (should be used in search and sort queries).
     *
     * @return string
     */
    public function getEmailCanonical();

    /**
     * @param string $emailCanonical
     */
    public function setEmailCanonical($emailCanonical);

    /**
     * @return CustomerInterface
     */
    public function getCustomer();

    /**
     * @param CustomerInterface $customer
     */
    public function setCustomer(CustomerInterface $customer = null);

    /**
     * @param string $username
     */
    public function setUsername($username);

    /**
     * Gets normalized username (should be used in search and sort queries).
     *
     * @return string
     */
    public function getUsernameCanonical();

    /**
     * @param string $usernameCanonical
     */
    public function setUsernameCanonical($usernameCanonical);

    /**
     * @param bool $locked
     */
    public function setLocked($locked);

    /**
     * @return string
     */
    public function getConfirmationToken();

    /**
     * @param string $confirmationToken
     */
    public function setConfirmationToken($confirmationToken);

    /**
     * Sets the timestamp that the user requested a password reset.
     *
     * @param null|\DateTime $date
     */
    public function setPasswordRequestedAt(\DateTime $date = null);

    /**
     * Checks whether the password reset request has expired.
     *
     * @param \DateInterval $ttl Requests older than this time interval will be considered expired
     *
     * @return bool true if the user's password request is non expired, false otherwise
     */
    public function isPasswordRequestNonExpired(\DateInterval $ttl);

    /**
     * @param \DateTime $date
     */
    public function setCredentialsExpireAt(\DateTime $date = null);

    /**
     * @return \DateTime
     */
    public function getLastLogin();

    /**
     * @param \DateTime $time
     */
    public function setLastLogin(\DateTime $time = null);

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role);

    /**
     * This overwrites any previous roles.
     *
     * @param array $roles
     */
    public function setRoles(array $roles);

    /**
     * @param string $role
     */
    public function addRole($role);

    /**
     * @param string $role
     */
    public function removeRole($role);

    /**
     * Gets connected OAuth accounts.
     *
     * @return Collection|UserOAuthInterface[]
     */
    public function getOAuthAccounts();

    /**
     * Gets connected OAuth account.
     *
     * @param string $provider
     *
     * @return null|UserOAuthInterface
     */
    public function getOAuthAccount($provider);

    /**
     * Connects OAuth account.
     *
     * @param UserOAuthInterface $oauth
     */
    public function addOAuthAccount(UserOAuthInterface $oauth);
}
