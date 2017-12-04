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

namespace Sylius\Component\Addressing\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;

class Address implements AddressInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $firstName;

    /**
     * @var string|null
     */
    protected $lastName;

    /**
     * @var string|null
     */
    protected $phoneNumber;

    /**
     * @var string|null
     */
    protected $company;

    /**
     * @var string|null
     */
    protected $countryCode;

    /**
     * @var string|null
     */
    protected $provinceCode;

    /**
     * @var string|null
     */
    protected $provinceName;

    /**
     * @var string|null
     */
    protected $street;

    /**
     * @var string|null
     */
    protected $city;

    /**
     * @var string|null
     */
    protected $postcode;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
    public function getFullName(): string
    {
        return trim(sprintf('%s %s', $this->firstName, $this->lastName));
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
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * {@inheritdoc}
     */
    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setCountryCode(?string $countryCode): void
    {
        if (null === $countryCode) {
            $this->provinceCode = null;
        }

        $this->countryCode = $countryCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getProvinceCode(): ?string
    {
        return $this->provinceCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setProvinceCode(?string $provinceCode): void
    {
        if (null === $this->countryCode) {
            return;
        }

        $this->provinceCode = $provinceCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getProvinceName(): ?string
    {
        return $this->provinceName;
    }

    /**
     * {@inheritdoc}
     */
    public function setProvinceName(?string $provinceName): void
    {
        $this->provinceName = $provinceName;
    }

    /**
     * {@inheritdoc}
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * {@inheritdoc}
     */
    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    /**
     * {@inheritdoc}
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * {@inheritdoc}
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * {@inheritdoc}
     */
    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    /**
     * {@inheritdoc}
     */
    public function setPostcode(?string $postcode): void
    {
        $this->postcode = $postcode;
    }
}
