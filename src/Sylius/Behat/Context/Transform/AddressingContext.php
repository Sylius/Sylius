<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class AddressingContext implements Context
{
    /**
     * @var FactoryInterface
     */
    private $addressFactory;

    /**
     * @var CountryNameConverterInterface
     */
    private $countryNameConverter;

    /**
     * @param FactoryInterface $addressFactory
     * @param CountryNameConverterInterface $countryNameConverter
     */
    public function __construct(
        FactoryInterface $addressFactory,
        CountryNameConverterInterface $countryNameConverter
    ) {
        $this->addressFactory = $addressFactory;
        $this->countryNameConverter = $countryNameConverter;
    }

    /**
     * @Transform /^to "([^"]+)"$/
     * @Transform /^"([^"]+)" as shipping country$/
     */
    public function createNewAddress($countryName)
    {
        $countryCode = $this->countryNameConverter->convertToCode($countryName);

        return $this->createAddress($countryCode);
    }

    /**
     * @Transform /^address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)"$/
     * @Transform /^address is "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)"$/
     * @Transform /^address to "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)"$/
     */
    public function createNewAddressWith($cityName, $street, $postcode, $countryName, $customerName)
    {
        $countryCode = $this->countryNameConverter->convertToCode($countryName);
        $customerName = explode(' ', $customerName);
        
        return $this->createAddress($countryCode, $customerName[0], $customerName[1], $cityName, $street, $postcode);
    }

    /**
     * @Transform /^do not specify any (shipping|billing) address$/
     */
    public function createEmptyAddress()
    {
        return $this->addressFactory->createNew();
    }

    /**
     * @Transform /^"([^"]+)" addressed it to "([^"]+)", "([^"]+)" "([^"]+)" in the "([^"]+)"$/
     * @Transform /^of "([^"]+)" in the "([^"]+)", "([^"]+)" "([^"]+)", "([^"]+)"$/
     * @Transform /^addressed it to "([^"]+)", "([^"]+)", "([^"]+)" "([^"]+)" in the "([^"]+)"$/
     */
    public function createNewAddressWithName($name, $street, $postcode, $city, $countryName)
    {
        $countryCode = $this->countryNameConverter->convertToCode($countryName);
        $names = explode(" ", $name);

        return $this->createAddress($countryCode, $names[0], $names[1], $city, $street, $postcode);
    }

    /**
     * @Transform /^"([^"]+)" addressed it to "([^"]+)", "([^"]+)" "([^"]+)" in the "([^"]+)"$/
     */
    public function createNewAddressWithUser($name, $street, $postcode, $city, $countryName)
    {
        $countryCode = $this->countryNameConverter->convertToCode($countryName);
        $names = explode(" ", $name);

        return $this->createAddress($countryCode, $names[0], $names[1], $city, $street, $postcode);
    }

    /**
     * @param string $countryCode
     * @param string $firstName
     * @param string $lastName
     * @param string $city
     * @param string $street
     *
     * @return AddressInterface
     */
    private function createAddress(
        $countryCode = 'US',
        $firstName = 'John',
        $lastName = 'Doe',
        $city = 'Ankh Morpork',
        $street = 'Frost Alley',
        $postCode = '90210'
    ) {
        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();
        $address->setCountryCode($countryCode);
        $address->setFirstName($firstName);
        $address->setLastName($lastName);
        $address->setCity($city);
        $address->setStreet($street);
        $address->setPostcode($postCode);

        return $address;
    }
}
