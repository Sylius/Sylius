<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
use SyliusLabs\Polyfill\Symfony\Security\Core\Encoder\EncoderAwareInterface;
use SyliusLabs\Polyfill\Symfony\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface;

interface UserInterface extends
    AdvancedUserInterface,
    CredentialsHolderInterface,
    ResourceInterface,
    \Serializable,
    TimestampableInterface,
    ToggleableInterface,
    EncoderAwareInterface,
    PasswordHasherAwareInterface
{
    public const DEFAULT_ROLE = 'ROLE_USER';

    public function getEmail(): ?string;

    public function setEmail(?string $email): void;

    /**
     * Gets normalized email (should be used in search and sort queries).
     */
    public function getEmailCanonical(): ?string;

    public function setEmailCanonical(?string $emailCanonical): void;

    public function setUsername(?string $username): void;

    /**
     * Gets normalized username (should be used in search and sort queries).
     */
    public function getUsernameCanonical(): ?string;

    public function setUsernameCanonical(?string $usernameCanonical): void;

    public function setLocked(bool $locked): void;

    public function getEmailVerificationToken(): ?string;

    public function setEmailVerificationToken(?string $verificationToken): void;

    public function getPasswordResetToken(): ?string;

    public function setPasswordResetToken(?string $passwordResetToken): void;

    public function getPasswordRequestedAt(): ?\DateTimeInterface;

    public function setPasswordRequestedAt(?\DateTimeInterface $date): void;

    public function isPasswordRequestNonExpired(\DateInterval $ttl): bool;

    public function isVerified(): bool;

    public function getVerifiedAt(): ?\DateTimeInterface;

    public function setVerifiedAt(?\DateTimeInterface $verifiedAt): void;

    public function getExpiresAt(): ?\DateTimeInterface;

    public function setExpiresAt(?\DateTimeInterface $date): void;

    public function getCredentialsExpireAt(): ?\DateTimeInterface;

    public function setCredentialsExpireAt(?\DateTimeInterface $date): void;

    public function getLastLogin(): ?\DateTimeInterface;

    public function setLastLogin(?\DateTimeInterface $time): void;

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     */
    public function hasRole(string $role): bool;

    public function addRole(string $role): void;

    public function removeRole(string $role): void;

    /**
     * @return Collection<array-key, UserOAuthInterface>
     */
    public function getOAuthAccounts(): Collection;

    public function getOAuthAccount(string $provider): ?UserOAuthInterface;

    public function addOAuthAccount(UserOAuthInterface $oauth): void;

    public function setEncoderName(?string $encoderName): void;

    public function __serialize(): array;

    public function __unserialize(array $data): void;
}
