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
 * Customer interface.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface CustomerInterface extends TimestampableInterface, SoftDeletableInterface
{
    /**
     * Gets email.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Sets the email.
     *
     * @param string $email
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
     * Sets the canonical email.
     *
     * @param string $emailCanonical
     * @return self
     */
    public function setEmailCanonical($emailCanonical);

    /**
     * Gets first and last name.
     *
     * @return string
     */
    public function getFullName();

    /**
     * Gets first name.
     *
     * @return string
     */
    public function getFirstName();

    /**
     * Sets first name
     *
     * @param string $firstName
     * @return self
     */
    public function setFirstName($firstName);

    /**
     * Gets last name.
     *
     * @return string
     */
    public function getLastName();

    /**
     * Sets last name.
     *
     * @param string $lastName
     * @return self
     */
    public function setLastName($lastName);
}
