<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This model was inspired by FOS User-Bundle
 */

namespace Sylius\Component\User\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\SoftDeletableInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * User interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface UserInterface extends AdvancedUserInterface, \Serializable, TimestampableInterface, SoftDeletableInterface
{
    const DEFAULT_ROLE = 'ROLE_USER';
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param  string $email
     * @return self
     */
    public function setEmail($email);

    /**
     * Gets the canonical email in search and sort queries.
     *
     * @return string
     */
    public function getEmailCanonical();

    /**
     * @param  string $emailCanonical
     * @return self
     */
    public function setEmailCanonical($emailCanonical);

    /**
     * @return CustomerInterface
     */
    public function getCustomer();

    /**
     * @param CustomerInterface $customer
     *
     * @return self
     */
    public function setCustomer(CustomerInterface $customer = null);

    /**
     * @param string $username
     *
     * @return self
     */
    public function setUsername($username);

    /**
     * Gets the canonical username in search and sort queries.
     *
     * @return string
     */
    public function getUsernameCanonical();

    /**
     * @param string $usernameCanonical
     *
     * @return self
     */
    public function setUsernameCanonical($usernameCanonical);

    /**
     * @return string
     */
    public function getPlainPassword();

    /**
     * @param string $password
     *
     * @return self
     */
    public function setPlainPassword($password);

    /**
     * Sets the hashed password.
     *
     * @param string $password
     *
     * @return self
     */
    public function setPassword($password);

    /**
     * Sets the enabled flag of the user.
     *
     * @param boolean $boolean
     *
     * @return self
     */
    public function setEnabled($boolean);

    /**
     * Sets the locking status of the user.
     *
     * @param boolean $boolean
     *
     * @return self
     */
    public function setLocked($boolean);

    /**
     * @return string
     */
    public function getConfirmationToken();

    /**
     * @param string $confirmationToken
     *
     * @return self
     */
    public function setConfirmationToken($confirmationToken);

    /**
     * Sets the timestamp that the user requested a password reset.
     *
     * @param null|\DateTime $date
     *
     * @return self
     */
    public function setPasswordRequestedAt(\DateTime $date = null);

    /**
     * Checks whether the password reset request has expired.
     *
     * @param \DateInterval $ttl Requests older than this time interval will be considered expired
     *
     * @return boolean true if the user's password request is non expired, false otherwise
     */
    public function isPasswordRequestNonExpired(\DateInterval $ttl);

    /**
     * Gets the last login time.
     *
     * @return \DateTime
     */
    public function getLastLogin();

    /**
     * Sets the last login time
     *
     * @param \DateTime $time
     *
     * @return self
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
     * @return boolean
     */
    public function hasRole($role);

    /**
     * Sets the roles of the user.
     *
     * This overwrites any previous roles.
     *
     * @param array $roles
     *
     * @return self
     */
    public function setRoles(array $roles);

    /**
     * @param string $role
     *
     * @return self
     */
    public function addRole($role);

    /**
     * @param string $role
     *
     * @return self
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
     *
     * @return self
     */
    public function addOAuthAccount(UserOAuthInterface $oauth);
}
