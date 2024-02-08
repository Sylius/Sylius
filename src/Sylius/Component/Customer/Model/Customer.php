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

use Sylius\Component\Resource\Model\TimestampableTrait;

class Customer implements CustomerInterface, \Stringable
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $email;

    /** @var string|null */
    protected $emailCanonical;

    /** @var string|null */
    protected $firstName;

    /** @var string|null */
    protected $lastName;

    /** @var \DateTimeInterface|null */
    protected $birthday;

    /** @var string */
    protected $gender = CustomerInterface::UNKNOWN_GENDER;

    /** @var CustomerGroupInterface|null */
    protected $group;

    /** @var string|null */
    protected $phoneNumber;

    /** @var bool */
    protected $subscribedToNewsletter = false;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function __toString(): string
    {
        return (string) $this->getEmail();
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

    public function getFullName(): string
    {
        return trim(sprintf('%s %s', $this->firstName, $this->lastName));
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): void
    {
        $this->birthday = $birthday;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): void
    {
        $this->gender = $gender;
    }

    public function isMale(): bool
    {
        return CustomerInterface::MALE_GENDER === $this->gender;
    }

    public function isFemale(): bool
    {
        return CustomerInterface::FEMALE_GENDER === $this->gender;
    }

    public function getGroup(): ?CustomerGroupInterface
    {
        return $this->group;
    }

    public function setGroup(?CustomerGroupInterface $group): void
    {
        $this->group = $group;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function isSubscribedToNewsletter(): bool
    {
        return $this->subscribedToNewsletter;
    }

    public function setSubscribedToNewsletter(bool $subscribedToNewsletter): void
    {
        $this->subscribedToNewsletter = $subscribedToNewsletter;
    }
}
