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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;

class User implements UserInterface, \Stringable
{
    use TimestampableTrait, ToggleableTrait;

    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $username;

    /**
     * Normalized representation of a username.
     *
     * @var string|null
     */
    protected $usernameCanonical;

    /**
     * Random data that is used as an additional input to a function that hashes a password.
     *
     * @var string|null
     */
    protected $salt;

    /**
     * Encrypted password. Must be persisted.
     *
     * @var string|null
     */
    protected $password;

    /**
     * Password before encryption. Used for model validation. Must not be persisted.
     *
     * @var string|null
     */
    protected $plainPassword;

    /** @var \DateTimeInterface|null */
    protected $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it
     *
     * @var string|null
     */
    protected $emailVerificationToken;

    /**
     * Random string sent to the user email address in order to verify the password resetting request
     *
     * @var string|null
     */
    protected $passwordResetToken;

    /** @var \DateTimeInterface|null */
    protected $passwordRequestedAt;

    /** @var \DateTimeInterface|null */
    protected $verifiedAt;

    /** @var bool */
    protected $locked = false;

    /** @var \DateTimeInterface|null */
    protected $expiresAt;

    /** @var \DateTimeInterface|null */
    protected $credentialsExpireAt;

    /**
     * We need at least one role to be able to authenticate
     *
     * @var mixed[]
     */
    protected $roles = [UserInterface::DEFAULT_ROLE];

    /** @var Collection<array-key, UserOAuthInterface> */
    protected $oauthAccounts;

    /** @var string|null */
    protected $email;

    /** @var string|null */
    protected $emailCanonical;

    /** @var string|null */
    protected $encoderName;

    public function __construct()
    {
        $this->salt = base_convert(bin2hex(random_bytes(20)), 16, 36);

        /** @var ArrayCollection<array-key, UserOAuthInterface> $this->oauthAccounts */
        $this->oauthAccounts = new ArrayCollection();

        $this->createdAt = new \DateTime();

        // Set here to overwrite default value from trait
        $this->enabled = false;
    }

    public function __toString(): string
    {
        return (string) $this->getUsername();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getEmailCanonical(): ?string
    {
        return $this->emailCanonical;
    }

    public function setEmailCanonical(?string $emailCanonical): void
    {
        $this->emailCanonical = $emailCanonical;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getUsernameCanonical(): ?string
    {
        return $this->usernameCanonical;
    }

    public function setUsernameCanonical(?string $usernameCanonical): void
    {
        $this->usernameCanonical = $usernameCanonical;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->usernameCanonical;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $encodedPassword): void
    {
        $this->password = $encodedPassword;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeInterface $date): void
    {
        $this->expiresAt = $date;
    }

    public function getCredentialsExpireAt(): ?\DateTimeInterface
    {
        return $this->credentialsExpireAt;
    }

    public function setCredentialsExpireAt(?\DateTimeInterface $date): void
    {
        $this->credentialsExpireAt = $date;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $time): void
    {
        $this->lastLogin = $time;
    }

    public function getEmailVerificationToken(): ?string
    {
        return $this->emailVerificationToken;
    }

    public function setEmailVerificationToken(?string $verificationToken): void
    {
        $this->emailVerificationToken = $verificationToken;
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(?string $passwordResetToken): void
    {
        $this->passwordResetToken = $passwordResetToken;
    }

    public function isCredentialsNonExpired(): bool
    {
        return !$this->hasExpired($this->credentialsExpireAt);
    }

    public function isAccountNonExpired(): bool
    {
        return !$this->hasExpired($this->expiresAt);
    }

    public function setLocked(bool $locked): void
    {
        $this->locked = $locked;
    }

    public function isAccountNonLocked(): bool
    {
        return !$this->locked;
    }

    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function addRole(string $role): void
    {
        $role = strtoupper($role);
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    public function removeRole(string $role): void
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function isPasswordRequestNonExpired(\DateInterval $ttl): bool
    {
        if (null === $this->passwordRequestedAt) {
            return false;
        }

        $threshold = new \DateTime();
        $threshold->sub($ttl);

        return $threshold <= $this->passwordRequestedAt;
    }

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTimeInterface $date): void
    {
        $this->passwordRequestedAt = $date;
    }

    public function isVerified(): bool
    {
        return null !== $this->verifiedAt;
    }

    public function getVerifiedAt(): ?\DateTimeInterface
    {
        return $this->verifiedAt;
    }

    public function setVerifiedAt(?\DateTimeInterface $verifiedAt): void
    {
        $this->verifiedAt = $verifiedAt;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getOAuthAccounts(): Collection
    {
        return $this->oauthAccounts;
    }

    public function getOAuthAccount(string $provider): ?UserOAuthInterface
    {
        if ($this->oauthAccounts->isEmpty()) {
            return null;
        }

        $filtered = $this->oauthAccounts->filter(function (UserOAuthInterface $oauth) use ($provider): bool {
            return $provider === $oauth->getProvider();
        });

        if ($filtered->isEmpty()) {
            return null;
        }

        return $filtered->current();
    }

    public function addOAuthAccount(UserOAuthInterface $oauth): void
    {
        if (!$this->oauthAccounts->contains($oauth)) {
            $this->oauthAccounts->add($oauth);
            $oauth->setUser($this);
        }
    }

    public function getEncoderName(): ?string
    {
        return $this->encoderName;
    }

    public function setEncoderName(?string $encoderName): void
    {
        $this->encoderName = $encoderName;
    }

    public function getPasswordHasherName(): ?string
    {
        return $this->getEncoderName();
    }

    /**
     * The serialized data have to contain the fields used by the equals method and the username.
     */
    public function __serialize(): array
    {
        return [
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->locked,
            $this->enabled,
            $this->id,
            $this->encoderName,
        ];
    }

    /**
     * @internal
     *
     * @deprecated since Sylius 1.11 and will be removed in Sylius 2.0, use \Sylius\Component\User\Model\User::__serialize() or \serialize($user) in PHP 8.1 instead
     */
    public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    public function __unserialize(array $serialized): void
    {
        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $serialized = array_merge($serialized, array_fill(0, 2, null));

        [
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->locked,
            $this->enabled,
            $this->id,
            $this->encoderName,
        ] = $serialized;
    }

    /**
     * @param string $serialized
     *
     * @internal
     *
     * @deprecated since Sylius 1.11 and will be removed in Sylius 2.0, use \Sylius\Component\User\Model\User::__unserialize() or \unserialize($serialized) in PHP 8.1 instead
     */
    public function unserialize($serialized): void
    {
        $this->__unserialize(unserialize($serialized));
    }

    protected function hasExpired(?\DateTimeInterface $date): bool
    {
        return null !== $date && new \DateTime() >= $date;
    }
}
