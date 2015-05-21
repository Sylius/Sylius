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

use Sylius\Component\Resource\Model\SoftDeletableInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Customer interface.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface CustomerInterface extends UserAwareInterface, TimestampableInterface, SoftDeletableInterface
{
    const UNKNOWN_GENDER = 'u';
    const MALE_GENDER = 'm';
    const FEMALE_GENDER = 'f';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return boolean
     */
    public function hasUser();

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
     * Gets first and last name.
     *
     * @return string
     */
    public function getFullName();

    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @param  string $firstName
     * @return self
     */
    public function setFirstName($firstName);

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @param  string $lastName
     * @return self
     */
    public function setLastName($lastName);

    /**
     * @return \DateTime
     */
    public function getBirthday();

    /**
     * @param  \DateTime $birthday
     * @return self
     */
    public function setBirthday(\DateTime $birthday = null);

    /**
     * @return int
     */
    public function getGender();

    /**
     * @param  int  $gender
     * @return self
     */
    public function setGender($gender);

    /**
     * @return bool
     */
    public function isMale();

    /**
     * @return bool
     */
    public function isFemale();
}
