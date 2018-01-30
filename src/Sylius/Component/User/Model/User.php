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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;

class User implements UserInterface
{
    use TimestampableTrait, ToggleableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string|null
     */
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
     * @var string
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

    /**
     * @var \DateTimeInterface|null
     */
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

    /**
     * @var \DateTimeInterface|null
     */
    protected $passwordRequestedAt;

    /**
     * @var \DateTimeInterface|null
     */
    protected $verifiedAt;

    /**
     * @var bool
     */
    protected $locked = false;

    /**
     * @var \DateTimeInterface|null
     */
    protected $expiresAt;

    /**
     * @var \DateTimeInterface|null
     */
    protected $credentialsExpireAt;

    /**
     * We need at least one role to be able to authenticate
     *
     * @var array
     */
    protected $roles = [UserInterface::DEFAULT_ROLE];

    /**
     * @var Collection|UserOAuth[]
     */
    protected $oauthAccounts;

    /**
     * @var string|null
     */
    protected $email;

    /**
     * @var string|null
     */
    protected $emailCanonical;

    public function __construct()
    {
        $this->salt = base_convert(bin2hex(random_bytes(20)), 16, 36);
        $this->oauthAccounts = new ArrayCollection();
        $this->createdAt = new \DateTime();

        // Set here to overwrite default value from trait
        $this->enabled = false;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getUsername();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailCanonical(): ?string
    {
        return $this->emailCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailCanonical(?string $emailCanonical): void
    {
        $this->emailCanonical = $emailCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsernameCanonical(): ?string
    {
        return $this->usernameCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsernameCanonical(?string $usernameCanonical): void
    {
        $this->usernameCanonical = $usernameCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlainPassword(?string $password): void
    {
        $this->plainPassword = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiresAt(?\DateTimeInterface $date): void
    {
        $this->expiresAt = $date;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentialsExpireAt(): ?\DateTimeInterface
    {
        return $this->credentialsExpireAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCredentialsExpireAt(?\DateTimeInterface $date): void
    {
        $this->credentialsExpireAt = $date;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastLogin(?\DateTimeInterface $time): void
    {
        $this->lastLogin = $time;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailVerificationToken(): ?string
    {
        return $this->emailVerificationToken;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailVerificationToken(?string $verificationToken): void
    {
        $this->emailVerificationToken = $verificationToken;
    }

    /**
     * {@inheritdoc}
     */
    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    /**
     * {@inheritdoc}
     */
    public function setPasswordResetToken(?string $passwordResetToken): void
    {
        $this->passwordResetToken = $passwordResetToken;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired(): bool
    {
        return !$this->hasExpired($this->credentialsExpireAt);
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired(): bool
    {
        return !$this->hasExpired($this->expiresAt);
    }

    /**
     * {@inheritdoc}
     */
    public function setLocked(bool $locked): void
    {
        $this->locked = $locked;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked(): bool
    {
        return !$this->locked;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function addRole(string $role): void
    {
        $role = strtoupper($role);
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeRole(string $role): void
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordRequestNonExpired(\DateInterval $ttl): bool
    {
        if (null === $this->passwordRequestedAt) {
            return false;
        }

        $threshold = new \DateTime();
        $threshold->sub($ttl);

        return $threshold <= $this->passwordRequestedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setPasswordRequestedAt(?\DateTimeInterface $date): void
    {
        $this->passwordRequestedAt = $date;
    }

    /**
     * {@inheritdoc}
     */
    public function isVerified(): bool
    {
        return null !== $this->verifiedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getVerifiedAt(): ?\DateTimeInterface
    {
        return $this->verifiedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setVerifiedAt(?\DateTimeInterface $verifiedAt): void
    {
        $this->verifiedAt = $verifiedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getOAuthAccounts(): Collection
    {
        return $this->oauthAccounts;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function addOAuthAccount(UserOAuthInterface $oauth): void
    {
        if (!$this->oauthAccounts->contains($oauth)) {
            $this->oauthAccounts->add($oauth);
            $oauth->setUser($this);
        }
    }

    /**
     * The serialized data have to contain the fields used by the equals method and the username.
     *
     * @return string
     */
    public function serialize(): string
    {
        return serialize([
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->locked,
            $this->enabled,
            $this->id,
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        $data = unserialize($serialized);
        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = array_merge($data, array_fill(0, 2, null));

        [
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->locked,
            $this->enabled,
            $this->id
        ] = $data;
    }

    /**
     * @param \DateTimeInterface|null $date
     *
     * @return bool
     */
    protected function hasExpired(?\DateTimeInterface $date): bool
    {
        return null !== $date && new \DateTime() >= $date;
    }
}
