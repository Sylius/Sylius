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
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
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
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * @param FactoryInterface $addressFactory
     * @param CountryNameConverterInterface $countryNameConverter
     * @param AddressRepositoryInterface $addressRepository
     * @param RepositoryInterface $countryRepository
     */
    public function __construct(
        FactoryInterface $addressFactory,
        CountryNameConverterInterface $countryNameConverter,
        AddressRepositoryInterface $addressRepository,
        RepositoryInterface $countryRepository
    ) {
        $this->addressFactory = $addressFactory;
        $this->countryNameConverter = $countryNameConverter;
        $this->addressRepository = $addressRepository;
        $this->countryRepository = $countryRepository;
    }

    /**
     * @Transform /^to "([^"]+)"$/
     * @Transform /^"([^"]+)" based \w+ address$/
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
    public function createNewAddressWith($city, $street, $postcode, $countryName, $customerName)
    {
        $countryCode = $this->countryNameConverter->convertToCode($countryName);
        list($firstName, $lastName) = explode(' ', $customerName);

        return $this->createAddress($countryCode, $firstName, $lastName, $city, $street, $postcode);
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
     * @Transform /^"([^"]+)" addressed it to "([^"]+)", "([^"]+)" "([^"]+)" in the "([^"]+)"(?:|, "([^"]+)")$/
     * @Transform /^of "([^"]+)" in the "([^"]+)", "([^"]+)" "([^"]+)", "([^"]+)"(?:|, "([^"]+)")$/
     * @Transform /^addressed it to "([^"]+)", "([^"]+)", "([^"]+)" "([^"]+)" in the "([^"]+)"(?:|, "([^"]+)")$/
     * @Transform /^address (?:|is )"([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)"(?:|, "([^"]+)")$/
     * @Transform /^address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)"(?:|, "([^"]+)")$/
     */
    public function createNewAddressWithName($name, $street, $postcode, $city, $countryName, $provinceName = null)
    {
        $countryCode = $this->countryNameConverter->convertToCode($countryName);
        list($firstName, $lastName) = explode(' ', $name);

        return $this->createAddress($countryCode, $firstName, $lastName, $city, $street, $postcode, $provinceName);
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
     * @param string $countryCode
     * @param string $firstName
     * @param string $lastName
     * @param string $city
     * @param string $street
     * @param string $postCode
     * @param string $provinceName
     *
     * @return AddressInterface
     */
    private function createAddress(
        $countryCode = 'US',
        $firstName = 'John',
        $lastName = 'Doe',
        $city = 'Ankh Morpork',
        $street = 'Frost Alley',
        $postCode = '90210',
        $provinceName = null
    ) {
        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();
        $address->setCountryCode($countryCode);
        $address->setFirstName($firstName);
        $address->setLastName($lastName);
        $address->setCity($city);
        $address->setStreet($street);
        $address->setPostcode($postCode);

        if (null === $provinceName) {
            return $address;
        }

        /** @var CountryInterface $country */
        $country = $this->countryRepository->findOneBy(['code' => $countryCode]);

        if (null !== $country) {
            /** @var ProvinceInterface $province */
            foreach ($country->getProvinces() as $province) {
                if ($province->getName() === $provinceName) {
                    $address->setProvinceCode($province->getCode());

                    return $address;
                }
            }
        }

        $address->setProvinceName($provinceName);

        return $address;
    }
}
