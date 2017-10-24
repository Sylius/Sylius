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

namespace Sylius\Component\Customer\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface CustomerInterface extends TimestampableInterface, ResourceInterface
{
    public const UNKNOWN_GENDER = 'u';
    public const MALE_GENDER = 'm';
    public const FEMALE_GENDER = 'f';

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
     * @return string
     */
    public function getFullName(): string;

    /**
     * @return string|null
     */
    public function getFirstName(): ?string;

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void;

    /**
     * @return string|null
     */
    public function getLastName(): ?string;

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void;

    /**
     * @return \DateTimeInterface|null
     */
    public function getBirthday(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface|null $birthday
     */
    public function setBirthday(?\DateTimeInterface $birthday): void;

    /**
     * @return string
     */
    public function getGender(): string;

    /**
     * You should use interface constants for that.
     *
     * @param string $gender
     */
    public function setGender(string $gender): void;

    /**
     * @return bool
     */
    public function isMale(): bool;

    /**
     * @return bool
     */
    public function isFemale(): bool;

    /**
     * @return CustomerGroupInterface|null
     */
    public function getGroup(): ?CustomerGroupInterface;

    /**
     * @param CustomerGroupInterface|null $group
     */
    public function setGroup(?CustomerGroupInterface $group): void;

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string;

    /**
     * @param string|null $phoneNumber
     */
    public function setPhoneNumber(?string $phoneNumber): void;

    /**
     * @return bool
     */
    public function isSubscribedToNewsletter(): bool;

    /**
     * @param bool $subscribedToNewsletter
     */
    public function setSubscribedToNewsletter(bool $subscribedToNewsletter): void;
}
