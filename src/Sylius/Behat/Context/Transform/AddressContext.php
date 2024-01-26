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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class AddressContext implements Context
{
    public function __construct(
        private FactoryInterface $addressFactory,
        private CountryNameConverterInterface $countryNameConverter,
        private AddressRepositoryInterface $addressRepository,
        private ExampleFactoryInterface $exampleAddressFactory,
    ) {
    }

    /**
     * @Transform /^to "([^"]+)"$/
     * @Transform /^"([^"]+)" based \w+ address$/
     * @Transform /^"([^"]+)" based address$/
     */
    public function createNewAddress($countryName)
    {
        return $this->exampleAddressFactory->create([
            'country_code' => $this->countryNameConverter->convertToCode($countryName),
            'customer' => null,
        ]);
    }

    /**
     * @Transform /^address (?:as |is |to )"([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)"$/
     * @Transform /^"([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" specified as$/
     */
    public function createNewAddressWith($city, $street, $postcode, $countryName, $customerName)
    {
        [$firstName, $lastName] = explode(' ', $customerName);

        return $this->exampleAddressFactory->create([
            'country_code' => $this->countryNameConverter->convertToCode($countryName),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'company' => null,
            'customer' => null,
            'phone_number' => null,
            'city' => $city,
            'street' => $street,
            'postcode' => $postcode,
        ]);
    }

    /**
     * @Transform /^clear the (shipping|billing) address$/
     * @Transform /^do not specify any (shipping|billing) address$/
     */
    public function createEmptyAddress()
    {
        return $this->addressFactory->createNew();
    }

    /**
     * @Transform /^address for "([^"]+)" from "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)"$/
     * @Transform /^"([^"]+)" addressed it to "([^"]+)", "([^"]+)" "([^"]+)" in the "([^"]+)", "([^"]+)"$/
     * @Transform /^of "([^"]+)" in the "([^"]+)", "([^"]+)" "([^"]+)", "([^"]+)", "([^"]+)"$/
     * @Transform /^addressed it to "([^"]+)", "([^"]+)", "([^"]+)" "([^"]+)" in the "([^"]+)", "([^"]+)"$/
     * @Transform /^address (?:|is |as )"([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)"$/
     */
    public function createNewAddressWithNameAndProvince($name, $street, $postcode, $city, $countryName, $provinceName)
    {
        [$firstName, $lastName] = explode(' ', $name);

        return $this->exampleAddressFactory->create([
            'country_code' => $this->countryNameConverter->convertToCode($countryName),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'company' => null,
            'customer' => null,
            'phone_number' => null,
            'city' => $city,
            'street' => $street,
            'postcode' => $postcode,
            'province_name' => $provinceName,
        ]);
    }

    /**
     * @Transform /^"([^"]+)" addressed it to "([^"]+)", "([^"]+)" "([^"]+)" in the "([^"]+)"$/
     * @Transform /^of "([^"]+)" in the "([^"]+)", "([^"]+)" "([^"]+)", "([^"]+)"$/
     * @Transform /^addressed it to "([^"]+)", "([^"]+)", "([^"]+)" "([^"]+)" in the "([^"]+)"$/
     * @Transform /^address (?:|is |as )"([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)"$/
     * @Transform /^"([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" as its(?:| new) billing address$/
     * @Transform /^be shipped to "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)"$/
     */
    public function createNewAddressWithName($name, $street, $postcode, $city, $countryName)
    {
        [$firstName, $lastName] = explode(' ', $name);

        return $this->exampleAddressFactory->create([
            'country_code' => $this->countryNameConverter->convertToCode($countryName),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'company' => null,
            'customer' => null,
            'phone_number' => null,
            'city' => $city,
            'street' => $street,
            'postcode' => $postcode,
        ]);
    }

    /**
     * @Transform /^"([^"]+)" street$/
     */
    public function getByStreet($street)
    {
        $address = $this->addressRepository->findOneBy(['street' => $street]);
        Assert::notNull($address, sprintf('Cannot find address by %s street.', $street));

        return $address;
    }

    /**
     * @Transform /^address of "([^"]+)"$/
     * @Transform /^address belongs to "([^"]+)"$/
     */
    public function getByFullName(string $fullName): AddressInterface
    {
        [$firstName, $lastName] = explode(' ', $fullName);

        /** @var AddressInterface $address */
        $address = $this->addressRepository->findOneBy(['firstName' => $firstName, 'lastName' => $lastName]);
        Assert::notNull($address, sprintf('Cannot find address by %s full name.', $fullName));

        return $address;
    }
}
