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
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void;

    /**
     * Gets normalized email (should be used in search and sort queries).
     *
     * @return string|null
     */
    public function getEmailCanonical(): ?string;

    /**
     * @param string|null $emailCanonical
     */
    public function setEmailCanonical(?string $emailCanonical): void;

    /**
     * @param string|null $username
     */
    public function setUsername(?string $username): void;

    /**
     * Gets normalized username (should be used in search and sort queries).
     *
     * @return string|null
     */
    public function getUsernameCanonical(): ?string;

    /**
     * @param string|null $usernameCanonical
     */
    public function setUsernameCanonical(?string $usernameCanonical): void;

    /**
     * @param bool $locked
     */
    public function setLocked(bool $locked): void;

    /**
     * @return string|null
     */
    public function getEmailVerificationToken(): ?string;

    /**
     * @param string|null $verificationToken
     */
    public function setEmailVerificationToken(?string $verificationToken): void;

    /**
     * @return string|null
     */
    public function getPasswordResetToken(): ?string;

    /**
     * @param string|null $passwordResetToken
     */
    public function setPasswordResetToken(?string $passwordResetToken): void;

    /**
     * @return \DateTimeInterface|null
     */
    public function getPasswordRequestedAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface|null $date
     */
    public function setPasswordRequestedAt(?\DateTimeInterface $date): void;

    /**
     * @param \DateInterval $ttl
     *
     * @return bool
     */
    public function isPasswordRequestNonExpired(\DateInterval $ttl): bool;

    /**
     * @return bool
     */
    public function isVerified(): bool;

    /**
     * @return \DateTimeInterface|null
     */
    public function getVerifiedAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface|null $verifiedAt
     */
    public function setVerifiedAt(?\DateTimeInterface $verifiedAt): void;

    /**
     * @return \DateTimeInterface|null
     */
    public function getExpiresAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface|null $date
     */
    public function setExpiresAt(?\DateTimeInterface $date): void;

    /**
     * @return \DateTimeInterface|null
     */
    public function getCredentialsExpireAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface|null $date
     */
    public function setCredentialsExpireAt(?\DateTimeInterface $date): void;

    /**
     * @return \DateTimeInterface|null
     */
    public function getLastLogin(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface|null $time
     */
    public function setLastLogin(?\DateTimeInterface $time): void;

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
    public function hasRole(string $role): bool;

    /**
     * @param string $role
     */
    public function addRole(string $role): void;

    /**
     * @param string $role
     */
    public function removeRole(string $role): void;

    /**
     * @return Collection|UserOAuthInterface[]
     */
    public function getOAuthAccounts(): Collection;

    /**
     * @param string $provider
     *
     * @return UserOAuthInterface|null
     */
    public function getOAuthAccount(string $provider): ?UserOAuthInterface;

    /**
     * @param UserOAuthInterface $oauth
     */
    public function addOAuthAccount(UserOAuthInterface $oauth): void;
}
