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
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class AddressContext implements Context
{
    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var ObjectManager
     */
    private $customerManager;

    /**
     * @param AddressRepositoryInterface $addressRepository
     * @param ObjectManager $customerManager
     */
    public function __construct(AddressRepositoryInterface $addressRepository, ObjectManager $customerManager)
    {
        $this->addressRepository = $addressRepository;
        $this->customerManager = $customerManager;
    }

    /**
     * @Given /^(their) default (address is "[^"]+", "[^"]+", "[^"]+", "[^"]+" for "[^"]+")$/
     * @Given /^(their) default (address is "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+")$/
     */
    public function theirDefaultAddressIs(CustomerInterface $customer, AddressInterface $address)
    {
        $this->setDefaultAddressOfCustomer($customer, $address);
    }

    /**
     * @Given /^(my) default address is of "([^"]+)"$/
     */
    public function myDefaultAddressIsOf(ShopUserInterface $user, $fullName)
    {
        list($firstName, $lastName) = explode(' ', $fullName);

        /** @var AddressInterface $address */
        $address = $this->addressRepository->findOneBy(['firstName' => $firstName, 'lastName' => $lastName]);
        Assert::notNull($address, sprintf('The address of "%s" has not been found.', $fullName));

        /** @var CustomerInterface $customer */
        $customer = $user->getCustomer();

        $this->setDefaultAddressOfCustomer($customer, $address);
    }

    /**
     * @Given /^(I) have an (address "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+"(?:|, "[^"]+")) in my address book$/
     */
    public function iHaveAnAddressInAddressBook(ShopUserInterface $user, AddressInterface $address)
    {
        /** @var CustomerInterface $customer */
        $customer = $user->getCustomer();

        $this->addAddressToCustomer($customer, $address);
    }

    /**
     * @Given /^(this customer) has an (address "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+"(?:|, "[^"]+")) in their address book$/
     * @Given /^(this customer) has an? ("[^"]+" based address) in their address book$/
     */
    public function thisCustomerHasAnAddressInAddressBook(CustomerInterface $customer, AddressInterface $address)
    {
        $this->addAddressToCustomer($customer, $address);
    }

    /**
     * @param CustomerInterface $customer
     * @param AddressInterface $address
     */
    private function addAddressToCustomer(CustomerInterface $customer, AddressInterface $address)
    {
        $customer->addAddress($address);

        $this->customerManager->flush();
    }

    /**
     * @param CustomerInterface $customer
     * @param AddressInterface $address
     */
    private function setDefaultAddressOfCustomer(CustomerInterface $customer, AddressInterface $address)
    {
        $customer->setDefaultAddress($address);

        $this->customerManager->flush();
    }
}
