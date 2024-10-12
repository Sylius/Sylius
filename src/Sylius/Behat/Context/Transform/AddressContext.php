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
use Sylius\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final readonly class AddressContext implements Context
{
    /** @param FactoryInterface<AddressInterface> $addressFactory */
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
    public function createNewAddress(string $countryName): AddressInterface
    {
        /** @var AddressInterface $address */
        $address = $this->exampleAddressFactory->create([
            'country_code' => $this->countryNameConverter->convertToCode($countryName),
            'customer' => null,
        ]);

        return $address;
    }

    /**
     * @Transform /^address (?:as |is |to )"([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)"$/
     * @Transform /^"([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" specified as$/
     */
    public function createNewAddressWith(string $city, string $street, string $postcode, string $countryName, string $customerName): AddressInterface
    {
        [$firstName, $lastName] = explode(' ', $customerName);

        /** @var AddressInterface $address */
        $address = $this->exampleAddressFactory->create([
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

        return $address;
    }

    /**
     * @Transform /^clear the (shipping|billing) address$/
     * @Transform /^do not specify any (shipping|billing) address$/
     */
    public function createEmptyAddress(): AddressInterface
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
    public function createNewAddressWithNameAndProvince(
        string $name,
        string $street,
        string $postcode,
        string $city,
        string $countryName,
        string $provinceName,
    ): AddressInterface {
        [$firstName, $lastName] = explode(' ', $name);

        /** @var AddressInterface $address */
        $address = $this->exampleAddressFactory->create([
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

        return $address;
    }

    /**
     * @Transform /^"([^"]+)" addressed it to "([^"]+)", "([^"]+)" "([^"]+)" in the "([^"]+)"$/
     * @Transform /^of "([^"]+)" in the "([^"]+)", "([^"]+)" "([^"]+)", "([^"]+)"$/
     * @Transform /^addressed it to "([^"]+)", "([^"]+)", "([^"]+)" "([^"]+)" in the "([^"]+)"$/
     * @Transform /^address (?:|is |as )"([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)"$/
     * @Transform /^"([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" as its(?:| new) billing address$/
     * @Transform /^be shipped to "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)"$/
     */
    public function createNewAddressWithName(
        string $name,
        string $street,
        string $postcode,
        string $city,
        string $countryName,
    ): AddressInterface {
        [$firstName, $lastName] = explode(' ', $name);

        /** @var AddressInterface $address */
        $address = $this->exampleAddressFactory->create([
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

        return $address;
    }

    /**
     * @Transform /^"([^"]+)" street$/
     */
    public function getByStreet(string $street): AddressInterface
    {
        /** @var AddressInterface $address */
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
