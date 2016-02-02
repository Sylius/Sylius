<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class UserContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $userRepository;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $userFactory;

    /**
     * @var FactoryInterface
     */
    private $customerFactory;

    /**
     * @var FactoryInterface
     */
    private $addressFactory;

    /**
     * @param RepositoryInterface $userRepository
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $userFactory
     * @param FactoryInterface $customerFactory
     * @param FactoryInterface $addressFactory
     */
    public function __construct(
        RepositoryInterface $userRepository,
        SharedStorageInterface $sharedStorage,
        FactoryInterface $userFactory,
        FactoryInterface $customerFactory,
        FactoryInterface $addressFactory
    ) {
        $this->userRepository = $userRepository;
        $this->sharedStorage = $sharedStorage;
        $this->userFactory = $userFactory;
        $this->customerFactory = $customerFactory;
        $this->addressFactory = $addressFactory;
    }

    /**
     * @Given there is user :email identified by :password
     */
    public function thereIsUserIdentifiedBy($email, $password)
    {
        $customer = $this->createCustomer();
        $user = $this->createUser($customer, $email, $password);

        $this->sharedStorage->setCurrentResource('user', $user);
        $this->userRepository->add($user);
    }

    /**
     * @Given there is user :email identified by :password, with :country as shipping country
     */
    public function thereIsUserWithShippingCountry($email, $password, $country)
    {
        $customer = $this->createCustomer();
        $user = $this->createUser($customer, $email, $password);

        $customer->setShippingAddress($this->createAddress($customer->getFirstName(), $customer->getLastName(), $country));

        $this->sharedStorage->setCurrentResource('user', $user);
        $this->userRepository->add($user);
    }

    /**
     * @param string $firstName
     * @param string $lastName
     *
     * @return CustomerInterface
     */
    private function createCustomer($firstName = 'John', $lastName = 'Doe')
    {
        $customer = $this->customerFactory->createNew();
        $customer->setFirstName($firstName);
        $customer->setLastName($lastName);

        return $customer;
    }

    /**
     * @param CustomerInterface $customer
     * @param string $email
     * @param string $password
     *
     * @return UserInterface
     */
    private function createUser(
        CustomerInterface $customer,
        $email = 'john.doe@example.com',
        $password = 'testPassword'
    ) {
        $user = $this->userFactory->createNew();

        $user->setCustomer($customer);
        $user->setUsername($email);
        $user->setEmail($email);
        $user->setPlainPassword($password);

        return $user;
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $country
     * @param string $street
     * @param string $city
     * @param string $postcode
     *
     * @return AddressInterface
     */
    private function createAddress(
        $firstName,
        $lastName,
        $country = 'United States',
        $street = 'Jones St. 114',
        $city = 'Paradise City',
        $postcode = '99999'
    ) {
        $address = $this->addressFactory->createNew();
        $address->setFirstName($firstName);
        $address->setLastName($lastName);
        $address->setStreet($street);
        $address->setCity($city);
        $address->setPostcode($postcode);
        $address->setCountryCode($this->getCountryCodeByEnglishCountryName($country));

        return $address;
    }

    /**
     * @param string $name
     *
     * @return string
     *
     * @throws \InvalidArgumentException If name is not found in country code registry.
     */
    private function getCountryCodeByEnglishCountryName($name)
    {
        $names = Intl::getRegionBundle()->getCountryNames('en');
        $countryCode = array_search(trim($name), $names);

        if (null === $countryCode) {
            throw new \InvalidArgumentException(sprintf(
                'Country "%s" not found! Available names: %s.', $name, implode(', ', $names)
            ));
        }

        return $countryCode;
    }
}
