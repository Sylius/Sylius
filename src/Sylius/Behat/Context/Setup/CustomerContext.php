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
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
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
     * @param SharedStorageInterface $sharedStorage
     * @param CustomerRepositoryInterface $customerRepository
     * @param ObjectManager $customerManager
     * @param FactoryInterface $customerFactory
     * @param FactoryInterface $userFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CustomerRepositoryInterface $customerRepository,
        ObjectManager $customerManager,
        FactoryInterface $customerFactory,
        FactoryInterface $userFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->customerRepository = $customerRepository;
        $this->customerManager = $customerManager;
        $this->customerFactory = $customerFactory;
        $this->userFactory = $userFactory;
    }

    /**
     * @Given the store has customer :name with email :email
     */
    public function theStoreHasCustomerWithNameAndEmail($name, $email)
    {
        $partsOfName = explode(' ', $name);
        $this->createCustomer($email, $partsOfName[0], $partsOfName[1]);
    }

    /**
     * @Given the store has customer :email
     */
    public function theStoreHasCustomer($email)
    {
        $this->createCustomer($email);
    }

    /**
     * @Given the store has customer :email with first name :firstName
     */
    public function theStoreHasCustomerWithFirstName($email, $firstName)
    {
        $this->createCustomer($email, $firstName);
    }

    /**
     * @Given the store has customer :email with last name :lastName
     */
    public function theStoreHasCustomerWithLastName($email, $lastName)
    {
        $this->createCustomer($email, null, $lastName);
    }

    /**
     * @Given the store has customer :email with name :fullName since :since
     */
    public function theStoreHasCustomerWithNameAndRegistrationDate($email, $fullName, $since)
    {
        $names = explode(' ', $fullName);
        $this->createCustomer($email, $names[0], $names[1], new \DateTime($since));
    }

    /**
     * @Given there is disabled customer account :email with password :password
     */
    public function thereIsDisabledCustomerAccountWithPassword($email, $password)
    {
        $this->createCustomerWithUserAccount($email, $password, false);
    }

    /**
     * @Given there is enabled customer account :email with password :password
     * @Given there is a customer account :email identified by :password
     */
    public function theStoreHasEnabledCustomerAccountWithPassword($email, $password)
    {
        $this->createCustomerWithUserAccount($email, $password, true);
    }

    /**
     * @Given there is a customer :name identified by an email :email and a password :password
     */
    public function theStoreHasCustomerAccountWithEmailAndPassword($name, $email, $password)
    {
        $names = explode(' ', $name);
        $firstName = $names[0];
        $lastName = count($names) > 1 ? $names[1] : null;

        $this->createCustomerWithUserAccount($email, $password, true, $firstName, $lastName);
    }

    /**
     * @Given there is an administrator :name identified by an email :email and a password :password
     */
    public function thereIsAdministratorIdentifiedByEmailAndPassword($name, $email, $password)
    {
        $names = explode(' ', $name);
        $firstName = $names[0];
        $lastName = count($names) > 1 ? $names[1] : null;

        $this->createCustomerWithUserAccount($email, $password, true, $firstName, $lastName, 'ROLE_ADMINISTRATION_ACCESS');
    }

    /**
     * @Given /^(his) default (address is "(?:[^"]+)", "(?:[^"]+)", "(?:[^"]+)", "(?:[^"]+)" for "(?:[^"]+)")$/
     * @Given /^(his) default (address is "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)")$/
     */
    public function heHasDefaultAddress(CustomerInterface $customer, AddressInterface $address)
    {
        $customer->setDefaultAddress($address);

        $this->customerManager->flush();
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
     * @Given /^(I) have an (address "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+"(?:|, "[^"]+")) in my address book$/
     */
    public function iHaveAnAddressInAddressBook(ShopUserInterface $user, AddressInterface $address)
    {
        /** @var CustomerInterface $customer */
        $customer = $user->getCustomer();

        $this->thisCustomerHasAnAddressInAddressBook($customer, $address);
    }

    /**
     * @Given /^(this customer) has an (address "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+"(?:|, "[^"]+")) in their address book$/
     */
    public function thisCustomerHasAnAddressInAddressBook(CustomerInterface $customer, AddressInterface $address)
    {
        $customer->addAddress($address);

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
     * @param string $email
     * @param string|null $firstName
     * @param string|null $lastName
     * @param \DateTime|null $createdAt
     */
    private function createCustomer($email, $firstName = null, $lastName = null, \DateTime $createdAt = null)
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();

        $customer->setFirstName($firstName);
        $customer->setLastName($lastName);
        $customer->setEmail($email);
        if (null !== $createdAt) {
            $customer->setCreatedAt($createdAt);
        }

        $this->sharedStorage->set('customer', $customer);
        $this->customerRepository->add($customer);
    }

    /**
     * @param string $email
     * @param string $password
     * @param bool $enabled
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $role
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
        $this->customerRepository->add($customer);
    }
}
