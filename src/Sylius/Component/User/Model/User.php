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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class User implements UserInterface
{
    use TimestampableTrait, ToggleableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * Normalized representation of a username.
     *
     * @var string
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
     * @var string
     */
    protected $password;

    /**
     * Password before encryption. Used for model validation. Must not be persisted.
     *
     * @var string
     */
    protected $plainPassword;

    /**
     * @var \DateTime
     */
    protected $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it
     *
     * @var string
     */
    protected $emailVerificationToken;

    /**
     * Random string sent to the user email address in order to verify the password resetting request
     *
     * @var string
     */
    protected $passwordResetToken;

    /**
     * @var \DateTime
     */
    protected $passwordRequestedAt;

    /**
     * @var \DateTime
     */
    protected $verifiedAt;

    /**
     * @var bool
     */
    protected $locked = false;

    /**
     * @var \DateTime
     */
    protected $expiresAt;

    /**
     * @var \DateTime
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
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $emailCanonical;

    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->oauthAccounts = new ArrayCollection();
        $this->createdAt = new \DateTime();

        // Set here to overwrite default value from trait
        $this->enabled = false;
    }

    /**
     * @return string
     */
    public function __toString()
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsernameCanonical()
    {
        return $this->usernameCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsernameCanonical($usernameCanonical)
    {
        $this->usernameCanonical = $usernameCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param \DateTime $date
     */
    public function setExpiresAt(\DateTime $date = null)
    {
        $this->expiresAt = $date;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentialsExpireAt()
    {
        return $this->credentialsExpireAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCredentialsExpireAt(\DateTime $date = null)
    {
        $this->credentialsExpireAt = $date;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastLogin(\DateTime $time = null)
    {
        $this->lastLogin = $time;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailVerificationToken()
    {
        return $this->emailVerificationToken;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailVerificationToken($verificationToken)
    {
        $this->emailVerificationToken = $verificationToken;
    }

    /**
     * {@inheritdoc}
     */
    public function getPasswordResetToken()
    {
        return $this->passwordResetToken;
    }

    /**
     * {@inheritdoc}
     */
    public function setPasswordResetToken($passwordResetToken)
    {
        $this->passwordResetToken = $passwordResetToken;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return !$this->hasExpired($this->credentialsExpireAt);
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return !$this->hasExpired($this->expiresAt);
    }

    /**
     * {@inheritdoc}
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return !$this->locked;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordRequestNonExpired(\DateInterval $ttl)
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
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setPasswordRequestedAt(\DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;
    }

    /**
     * {@inheritdoc}
     */
    public function isVerified()
    {
        return null !== $this->verifiedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getVerifiedAt()
    {
        return $this->verifiedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setVerifiedAt(\DateTime $verifiedAt = null)
    {
        $this->verifiedAt = $verifiedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getOAuthAccounts()
    {
        return $this->oauthAccounts;
    }

    /**
     * {@inheritdoc}
     */
    public function getOAuthAccount($provider)
    {
        if ($this->oauthAccounts->isEmpty()) {
            return null;
        }

        $filtered = $this->oauthAccounts->filter(function (UserOAuthInterface $oauth) use ($provider) {
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
    public function addOAuthAccount(UserOAuthInterface $oauth)
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
    public function serialize()
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
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = array_merge($data, array_fill(0, 2, null));

        list(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->locked,
            $this->enabled,
            $this->id
        ) = $data;
    }

    /**
     * @param \DateTime $date
     *
     * @return bool
     */
    protected function hasExpired(\DateTime $date = null)
    {
        return null !== $date && new \DateTime() >= $date;
    }
}
