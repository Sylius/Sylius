<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Customer\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface CustomerInterface extends TimestampableInterface, ResourceInterface
{
    const UNKNOWN_GENDER = 'u';
    const MALE_GENDER = 'm';
    const FEMALE_GENDER = 'f';

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param  string $email
     */
    public function setEmail($email);

    /**
     * Gets normalized email (should be used in search and sort queries).
     *
     * @return string
     */
    public function getEmailCanonical();

    /**
     * @param  string $emailCanonical
     */
    public function setEmailCanonical($emailCanonical);

    /**
     * @return string
     */
    public function getFullName();

    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @param  string $firstName
     */
    public function setFirstName($firstName);

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @param  string $lastName
     */
    public function setLastName($lastName);

    /**
     * @return \DateTime
     */
    public function getBirthday();

    /**
     * @param  \DateTime $birthday
     */
    public function setBirthday(\DateTime $birthday = null);

    /**
     * @return string
     */
    public function getGender();

    /**
     * You should use interface constants for that.
     *
     * @param  string $gender
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

    /**
     * @return CustomerGroupInterface
     */
    public function getGroup();

    /**
     * @param CustomerGroupInterface $group
     */
    public function setGroup(CustomerGroupInterface $group = null);

    /**
     * @return string
     */
    public function getPhoneNumber();

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber);

    /**
     * @return bool
     */
    public function isSubscribedToNewsletter();

    /**
     * @param bool $subscribedToNewsletter
     */
    public function setSubscribedToNewsletter($subscribedToNewsletter);
}
