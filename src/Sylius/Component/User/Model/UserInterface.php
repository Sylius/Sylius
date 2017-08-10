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
    public const DEFAULT_ROLE = 'ROLE_USER';

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
    public function getEmailVerificationToken();

    /**
     * @param string $verificationToken
     */
    public function setEmailVerificationToken($verificationToken);

    /**
     * @return string
     */
    public function getPasswordResetToken();

    /**
     * @param string $passwordResetToken
     */
    public function setPasswordResetToken($passwordResetToken);

    /**
     * @return \DateTimeInterface|null
     */
    public function getPasswordRequestedAt();

    /**
     * @param \DateTimeInterface|null $date
     */
    public function setPasswordRequestedAt(\DateTimeInterface $date = null);

    /**
     * @param \DateInterval $ttl
     *
     * @return bool
     */
    public function isPasswordRequestNonExpired(\DateInterval $ttl);

    /**
     * @return bool
     */
    public function isVerified();

    /**
     * @return \DateTimeInterface|null
     */
    public function getVerifiedAt();

    /**
     * @param \DateTimeInterface|null $verifiedAt
     */
    public function setVerifiedAt(\DateTimeInterface $verifiedAt = null);

    /**
     * @param \DateTimeInterface|null $date
     */
    public function setCredentialsExpireAt(\DateTimeInterface $date = null);

    /**
     * @return \DateTimeInterface|null
     */
    public function getLastLogin();

    /**
     * @param \DateTimeInterface|null $time
     */
    public function setLastLogin(\DateTimeInterface $time = null);

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
     * @param string $role
     */
    public function addRole($role);

    /**
     * @param string $role
     */
    public function removeRole($role);

    /**
     * @return Collection|UserOAuthInterface[]
     */
    public function getOAuthAccounts();

    /**
     * @param string $provider
     *
     * @return UserOAuthInterface|null
     */
    public function getOAuthAccount($provider);

    /**
     * @param UserOAuthInterface $oauth
     */
    public function addOAuthAccount(UserOAuthInterface $oauth);
}
