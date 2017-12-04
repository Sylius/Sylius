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

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface AddressInterface extends TimestampableInterface, ResourceInterface
{
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
     * @return string|null
     */
    public function getFullName(): ?string;

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string;

    /**
     * @param string|null $phoneNumber
     */
    public function setPhoneNumber(?string $phoneNumber): void;

    /**
     * @return string|null
     */
    public function getCompany(): ?string;

    /**
     * @param string|null $company
     */
    public function setCompany(?string $company): void;

    /**
     * @return string|null
     */
    public function getCountryCode(): ?string;

    /**
     * @param string|null $countryCode
     */
    public function setCountryCode(?string $countryCode): void;

    /**
     * @return string|null
     */
    public function getProvinceCode(): ?string;

    /**
     * @param string|null $provinceCode
     */
    public function setProvinceCode(?string $provinceCode): void;

    /**
     * @return string|null
     */
    public function getProvinceName(): ?string;

    /**
     * @param string|null $provinceName
     */
    public function setProvinceName(?string $provinceName): void;

    /**
     * @return string|null
     */
    public function getStreet(): ?string;

    /**
     * @param string|null $street
     */
    public function setStreet(?string $street): void;

    /**
     * @return string|null
     */
    public function getCity(): ?string;

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void;

    /**
     * @return string|null
     */
    public function getPostcode(): ?string;

    /**
     * @param string|null $postcode
     */
    public function setPostcode(?string $postcode): void;
}
