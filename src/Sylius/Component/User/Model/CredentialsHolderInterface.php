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

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface CredentialsHolderInterface
{
    /**
     * @return string
     */
    public function getPlainPassword();

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword);

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string
     */
    public function getPassword();

    /**
     * @param string $encodedPassword
     */
    public function setPassword($encodedPassword);

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null
     */
    public function getSalt();

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials();
}
