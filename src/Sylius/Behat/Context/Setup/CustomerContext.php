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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\Address;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class CustomerContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var ObjectManager
     */
    private $customerManager;

    /**
     * @var FactoryInterface
     */
    private $customerFactory;

    /**
     * @var FactoryInterface
     */
    private $userFactory;

    /**
     * @var FactoryInterface
     */
    private $addressFactory;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param CustomerRepositoryInterface $customerRepository
     * @param ObjectManager $customerManager
     * @param FactoryInterface $customerFactory
     * @param FactoryInterface $userFactory
     * @param FactoryInterface $addressFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CustomerRepositoryInterface $customerRepository,
        ObjectManager $customerManager,
        FactoryInterface $customerFactory,
        FactoryInterface $userFactory,
        FactoryInterface $addressFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->customerRepository = $customerRepository;
        $this->customerManager = $customerManager;
        $this->customerFactory = $customerFactory;
        $this->userFactory = $userFactory;
        $this->addressFactory = $addressFactory;
    }

    /**
     * @Given the store has customer :name with email :email
     */
    public function theStoreHasCustomerWithNameAndEmail($name, $email)
    {
        $partsOfName = explode(' ', $name);
        $customer = $this->createCustomer($email, $partsOfName[0], $partsOfName[1]);
        $this->customerRepository->add($customer);
    }

    /**
     * @Given the store (also )has customer :email
     */
    public function theStoreHasCustomer($email)
    {
        $customer = $this->createCustomer($email);

        $this->customerRepository->add($customer);
    }

    /**
     * @Given the store has customer :email with first name :firstName
     */
    public function theStoreHasCustomerWithFirstName($email, $firstName)
    {
        $customer = $this->createCustomer($email, $firstName);

        $this->customerRepository->add($customer);
    }

    /**
     * @Given the store has customer :email with name :fullName since :since
     * @Given the store has customer :email with name :fullName and phone number :phoneNumber since :since
     */
    public function theStoreHasCustomerWithNameAndRegistrationDate($email, $fullName, $phoneNumber = null, $since)
    {
        $names = explode(' ', $fullName);
        $customer = $this->createCustomer($email, $names[0], $names[1], new \DateTime($since), $phoneNumber);

        $this->customerRepository->add($customer);
    }

    /**
     * @Given there is disabled customer account :email with password :password
     */
    public function thereIsDisabledCustomerAccountWithPassword($email, $password)
    {
        $customer = $this->createCustomerWithUserAccount($email, $password, false);
        $this->customerRepository->add($customer);
    }

    /**
     * @Given there is a customer account :email
     * @Given there is a customer account :email identified by :password
     * @Given there is enabled customer account :email with password :password
     */
    public function theStoreHasEnabledCustomerAccountWithPassword($email, $password = 'sylius')
    {
        $customer = $this->createCustomerWithUserAccount($email, $password, true);
        $this->customerRepository->add($customer);
    }

    /**
     * @Given there is a customer :name identified by an email :email and a password :password
     */
    public function theStoreHasCustomerAccountWithEmailAndPassword($name, $email, $password)
    {
        $names = explode(' ', $name);
        $firstName = $names[0];
        $lastName = count($names) > 1 ? $names[1] : null;

        $customer = $this->createCustomerWithUserAccount($email, $password, true, $firstName, $lastName);
        $this->customerRepository->add($customer);
    }

    /**
     * @Given /^(the customer) subscribed to the newsletter$/
     */
    public function theCustomerSubscribedToTheNewsletter(CustomerInterface $customer)
    {
        $customer->setSubscribedToNewsletter(true);

        $this->customerManager->flush();
    }

    /**
     * @Given /^(this customer) verified their email$/
     */
    public function theCustomerVerifiedTheirEmail(CustomerInterface $customer)
    {
        $customer->getUser()->setVerifiedAt(new \DateTime());

        $this->customerManager->flush();
    }

    /**
     * @Given /^(the customer) belongs to (group "([^"]+)")$/
     */
    public function theCustomerBelongsToGroup(CustomerInterface $customer, CustomerGroupInterface $customerGroup)
    {
        $customer->setGroup($customerGroup);

        $this->customerManager->flush();
    }

    /**
     * @Given there is user :email with :country as shipping country
     */
    public function thereIsUserIdentifiedByWithAsShippingCountry($email, CountryInterface $country)
    {
        $customer = $this->createCustomerWithUserAccount($email, 'password123', true, 'John', 'Doe');
        $address = $this->addressFactory->createNew();
        $address->setCountryCode($country->getCode());
        $address->setCity('Berlin');
        $address->setFirstName($customer->getFirstName());
        $address->setLastName($customer->getLastName());
        $address->setStreet('street');
        $address->setPostcode('123');
        $customer->setDefaultAddress($address);

        $this->customerRepository->add($customer);
    }

    /**
     * @param string $email
     * @param string|null $firstName
     * @param string|null $lastName
     * @param \DateTime|null $createdAt
     * @param string|null $phoneNumber
     *
     * @return CustomerInterface
     */
    private function createCustomer(
        $email,
        $firstName = null,
        $lastName = null,
        \DateTime $createdAt = null,
        $phoneNumber = null
    ) {
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();

        $customer->setFirstName($firstName);
        $customer->setLastName($lastName);
        $customer->setEmail($email);
        $customer->setPhoneNumber($phoneNumber);
        if (null !== $createdAt) {
            $customer->setCreatedAt($createdAt);
        }

        $this->sharedStorage->set('customer', $customer);

        return $customer;
    }

    /**
     * @param string $email
     * @param string $password
     * @param bool $enabled
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $role
     *
     * @return CustomerInterface
     */
    private function createCustomerWithUserAccount(
        $email,
        $password,
        $enabled = true,
        $firstName = null,
        $lastName = null,
        $role = null
    ) {
        $user = $this->userFactory->createNew();
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();

        $customer->setFirstName($firstName);
        $customer->setLastName($lastName);
        $customer->setEmail($email);

        $user->setUsername($email);
        $user->setPlainPassword($password);
        $user->setEnabled($enabled);
        $user->addRole($role);

        $customer->setUser($user);

        $this->sharedStorage->set('customer', $customer);

        return $customer;
    }
}
