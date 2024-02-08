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

namespace Sylius\Component\Customer\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface CustomerInterface extends TimestampableInterface, ResourceInterface
{
    public const UNKNOWN_GENDER = 'u';

    public const MALE_GENDER = 'm';

    public const FEMALE_GENDER = 'f';

    public function getEmail(): ?string;

    public function setEmail(?string $email): void;

    /**
     * Gets normalized email (should be used in search and sort queries).
     */
    public function getEmailCanonical(): ?string;

    public function setEmailCanonical(?string $emailCanonical): void;

    public function getFullName(): string;

    public function getFirstName(): ?string;

    public function setFirstName(?string $firstName): void;

    public function getLastName(): ?string;

    public function setLastName(?string $lastName): void;

    public function getBirthday(): ?\DateTimeInterface;

    public function setBirthday(?\DateTimeInterface $birthday): void;

    public function getGender(): string;

    /**
     * You should use interface constants for that.
     */
    public function setGender(string $gender): void;

    public function isMale(): bool;

    public function isFemale(): bool;

    public function getGroup(): ?CustomerGroupInterface;

    public function setGroup(?CustomerGroupInterface $group): void;

    public function getPhoneNumber(): ?string;

    public function setPhoneNumber(?string $phoneNumber): void;

    public function isSubscribedToNewsletter(): bool;

    public function setSubscribedToNewsletter(bool $subscribedToNewsletter): void;
}
