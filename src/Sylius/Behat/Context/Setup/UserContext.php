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
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Core\Test\Factory\TestUserFactoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
final class UserContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var TestUserFactoryInterface
     */
    private $userFactory;

    /**
     * @var FactoryInterface
     */
    private $addressFactory;

    /**
     * @var ObjectManager
     */
    private $userManager;

    /**
     * @var CountryNameConverterInterface
     */
    private $countryCodeConverter;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param UserRepositoryInterface $userRepository
     * @param TestUserFactoryInterface $userFactory
     * @param FactoryInterface $addressFactory
     * @param ObjectManager $userManager
     * @param CountryNameConverterInterface $countryCodeConverter
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        UserRepositoryInterface $userRepository,
        TestUserFactoryInterface $userFactory,
        FactoryInterface $addressFactory,
        ObjectManager $userManager,
        CountryNameConverterInterface $countryCodeConverter
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->userRepository = $userRepository;
        $this->userFactory = $userFactory;
        $this->addressFactory = $addressFactory;
        $this->userManager = $userManager;
        $this->countryCodeConverter = $countryCodeConverter;
    }

    /**
     * @Given there is user :email identified by :password
     * @Given there was account of :email with password :password
     */
    public function thereIsUserIdentifiedBy($email, $password)
    {
        $user = $this->userFactory->create($email, $password);

        $this->sharedStorage->set('user', $user);

        $this->userRepository->add($user);
    }

    /**
     * @Given there is user :email identified by :password, with :country as shipping country
     */
    public function thereIsUserWithShippingCountry($email, $password, $country)
    {
        $user = $this->userFactory->create($email, $password);

        $customer = $user->getCustomer();
        $customer->setShippingAddress($this->createAddress($customer->getFirstName(), $customer->getLastName(), $country));

        $this->sharedStorage->set('user', $user);
        $this->userRepository->add($user);
    }

    /**
     * @Given my default shipping address is :country
     */
    public function myDefaultShippingAddressIs($country)
    {
        $user = $this->sharedStorage->get('user');
        $customer = $user->getCustomer();
        $customer->setShippingAddress($this->createAddress($customer->getFirstName(), $customer->getLastName(), $country));

        $this->userManager->flush();
    }

    /**
     * @Given the account of :email was deleted
     */
    public function accountWasDeleted($email)
    {
        $user = $this->userRepository->findOneByEmail($email);

        $this->sharedStorage->set('customer', $user->getCustomer());

        $this->userRepository->remove($user);
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
        $address->setCountryCode($this->countryCodeConverter->convertToCode($country));

        return $address;
    }

    /**
     * @Given his account was deleted
     */
    public function hisAccountWasDeleted()
    {
        $user = $this->sharedStorage->get('user');

        $this->userRepository->remove($user);
    }
}
