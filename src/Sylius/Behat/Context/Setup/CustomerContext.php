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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

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
    public function theStoreHasCustomerWithNameAndEmail($name, $email): void
    {
        $partsOfName = explode(' ', $name);
        $customer = $this->createCustomer($email, $partsOfName[0], $partsOfName[1]);
        $this->customerRepository->add($customer);
    }

    /**
     * @Given the store (also )has customer :email
     */
    public function theStoreHasCustomer($email): void
    {
        $customer = $this->createCustomer($email);

        $this->customerRepository->add($customer);
    }

    /**
     * @Given the store has customer :email with first name :firstName
     */
    public function theStoreHasCustomerWithFirstName($email, $firstName): void
    {
        $customer = $this->createCustomer($email, $firstName);

        $this->customerRepository->add($customer);
    }

    /**
     * @Given the store has customer :email with name :fullName since :since
     * @Given the store has customer :email with name :fullName and phone number :phoneNumber since :since
     */
    public function theStoreHasCustomerWithNameAndRegistrationDate($email, $fullName, $phoneNumber = null, $since): void
    {
        $names = explode(' ', $fullName);
        $customer = $this->createCustomer($email, $names[0], $names[1], new \DateTime($since), $phoneNumber);

        $this->customerRepository->add($customer);
    }

    /**
     * @Given there is disabled customer account :email with password :password
     */
    public function thereIsDisabledCustomerAccountWithPassword($email, $password): void
    {
        $customer = $this->createCustomerWithUserAccount($email, $password, false);
        $this->customerRepository->add($customer);
    }

    /**
     * @Given there is a customer account :email
     * @Given there is a customer account :email identified by :password
     * @Given there is enabled customer account :email with password :password
     */
    public function theStoreHasEnabledCustomerAccountWithPassword($email, $password = 'sylius'): void
    {
        $customer = $this->createCustomerWithUserAccount($email, $password, true);
        $this->customerRepository->add($customer);
    }

    /**
     * @Given there is a customer :name identified by an email :email and a password :password
     * @Given there is a customer :name with an email :email and a password :password
     */
    public function theStoreHasCustomerAccountWithEmailAndPassword($name, $email, $password): void
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
    public function theCustomerSubscribedToTheNewsletter(CustomerInterface $customer): void
    {
        $customer->setSubscribedToNewsletter(true);

        $this->customerManager->flush();
    }

    /**
     * @Given /^(this customer) verified their email$/
     */
    public function theCustomerVerifiedTheirEmail(CustomerInterface $customer): void
    {
        $customer->getUser()->setVerifiedAt(new \DateTime());

        $this->customerManager->flush();
    }

    /**
     * @Given /^(the customer) belongs to (group "([^"]+)")$/
     */
    public function theCustomerBelongsToGroup(CustomerInterface $customer, CustomerGroupInterface $customerGroup): void
    {
        $customer->setGroup($customerGroup);

        $this->customerManager->flush();
    }

    /**
     * @Given there is user :email with :country as shipping country
     */
    public function thereIsUserIdentifiedByWithAsShippingCountry($email, CountryInterface $country): void
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
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $phoneNumber
     *
     * @return CustomerInterface
     */
    private function createCustomer(
        string $email,
        ?string $firstName = null,
        ?string $lastName = null,
        ?\DateTimeInterface $createdAt = null,
        ?string $phoneNumber = null
    ): CustomerInterface {
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
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $role
     *
     * @return CustomerInterface
     */
    private function createCustomerWithUserAccount(
        string $email,
        string $password,
        bool $enabled = true,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $role = null
    ): CustomerInterface {
        $user = $this->userFactory->createNew();
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();

        $customer->setFirstName($firstName);
        $customer->setLastName($lastName);
        $customer->setEmail($email);

        $user->setUsername($email);
        $user->setPlainPassword($password);
        $user->setEnabled($enabled);
        if (null !== $role) {
            $user->addRole($role);
        }

        $customer->setUser($user);

        $this->sharedStorage->set('customer', $customer);

        return $customer;
    }
}
