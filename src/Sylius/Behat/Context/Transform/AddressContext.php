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
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class AddressContext implements Context
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
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var ExampleFactoryInterface
     */
    private $exampleAddressFactory;

    /**
     * @param FactoryInterface $addressFactory
     * @param CountryNameConverterInterface $countryNameConverter
     * @param AddressRepositoryInterface $addressRepository
     * @param ExampleFactoryInterface $exampleAddressFactory
     */
    public function __construct(
        FactoryInterface $addressFactory,
        CountryNameConverterInterface $countryNameConverter,
        AddressRepositoryInterface $addressRepository,
        ExampleFactoryInterface $exampleAddressFactory
    ) {
        $this->addressFactory = $addressFactory;
        $this->countryNameConverter = $countryNameConverter;
        $this->addressRepository = $addressRepository;
        $this->exampleAddressFactory = $exampleAddressFactory;
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
     */
    public function createNewAddressWith($city, $street, $postcode, $countryName, $customerName)
    {
        list($firstName, $lastName) = explode(' ', $customerName);

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
     * @Transform /^clear old (shipping|billing) address$/
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
        list($firstName, $lastName) = explode(' ', $name);

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
     */
    public function createNewAddressWithName($name, $street, $postcode, $city, $countryName)
    {
        list($firstName, $lastName) = explode(' ', $name);

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
}
