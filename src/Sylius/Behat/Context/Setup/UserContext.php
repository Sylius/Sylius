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
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Core\Test\Factory\TestUserFactoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Intl\Intl;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class UserContext implements Context
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
     * @param RepositoryInterface $userRepository
     * @param SharedStorageInterface $sharedStorage
     * @param TestUserFactoryInterface $userFactory
     * @param FactoryInterface $addressFactory
     * @param ObjectManager $userManager
     */
    public function __construct(
        RepositoryInterface $userRepository,
        SharedStorageInterface $sharedStorage,
        TestUserFactoryInterface $userFactory,
        FactoryInterface $addressFactory,
        ObjectManager $userManager
    ) {
        $this->userRepository = $userRepository;
        $this->sharedStorage = $sharedStorage;
        $this->userFactory = $userFactory;
        $this->addressFactory = $addressFactory;
        $this->userManager = $userManager;
    }

    /**
     * @Given there is user :email identified by :password
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

    /**
     * @Given there is an administrator account
     */
    public function thereIsAdministratorAccount()
    {
        /** @var UserInterface $user */
        $user = $this->userFactory->createNew();
        $customer = $this->customerFactory->createNew();
        $customer->setFirstName('Admin');
        $customer->setLastName('Admin');

        $user->setCustomer($customer);
        $user->setUsername('Administrator Account');
        $user->setEmail('admin@test.com');
        $user->setPlainPassword('pswd1234');
        $user->addRole('ROLE_ADMINISTRATION_ACCESS');

        $this->sharedStorage->setCurrentResource('administrator', $user);
        $this->userRepository->add($user);
    }

    /**
     * @Given the account of :email was deleted
     */
    public function accountWasDeleted($email)
    {
        $user = $this->userRepository->findOneBy(array('username' => $email));

        $this->userRepository->remove($user);
    }
}
