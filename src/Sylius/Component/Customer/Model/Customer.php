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

use Sylius\Component\Resource\Model\TimestampableTrait;

class Customer implements CustomerInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $email;

    /**
     * @var string|null
     */
    protected $emailCanonical;

    /**
     * @var string|null
     */
    protected $firstName;

    /**
     * @var string|null
     */
    protected $lastName;

    /**
     * @var \DateTimeInterface|null
     */
    protected $birthday;

    /**
     * @var string
     */
    protected $gender = CustomerInterface::UNKNOWN_GENDER;

    /**
     * @var CustomerGroupInterface|null
     */
    protected $group;

    /**
     * @var string|null
     */
    protected $phoneNumber;

    /**
     * @var bool
     */
    protected $subscribedToNewsletter = false;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getEmail();
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
    public function getFullName(): string
    {
        return trim(sprintf('%s %s', $this->firstName, $this->lastName));
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    /**
     * {@inheritdoc}
     */
    public function setBirthday(?\DateTimeInterface $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * {@inheritdoc}
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * {@inheritdoc}
     */
    public function setGender(?string $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * {@inheritdoc}
     */
    public function isMale(): bool
    {
        return CustomerInterface::MALE_GENDER === $this->gender;
    }

    /**
     * {@inheritdoc}
     */
    public function isFemale(): bool
    {
        return CustomerInterface::FEMALE_GENDER === $this->gender;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup(): ?CustomerGroupInterface
    {
        return $this->group;
    }

    /**
     * {@inheritdoc}
     */
    public function setGroup(?CustomerGroupInterface $group): void
    {
        $this->group = $group;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function isSubscribedToNewsletter(): bool
    {
        return $this->subscribedToNewsletter;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubscribedToNewsletter(bool $subscribedToNewsletter): void
    {
        $this->subscribedToNewsletter = $subscribedToNewsletter;
    }
}
