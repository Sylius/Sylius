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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Webmozart\Assert\Assert;

final class AddressContext implements Context
{
    public function __construct(
        private AddressRepositoryInterface $addressRepository,
        private ObjectManager $customerManager,
        private SharedStorageInterface $sharedStorage,
    ) {
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
        [$firstName, $lastName] = explode(' ', $fullName);

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

        $this->sharedStorage->set('address', $address);
    }

    /**
     * @Given this address has province :province
     */
    public function thisAddressHasProvince(string $provinceName): void
    {
        $address = $this->sharedStorage->get('address');
        $address->setProvinceName($provinceName);

        $this->customerManager->flush();
    }

    /**
     * @Given /^(this customer) has an (address "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+"(?:|, "[^"]+")) in their address book$/
     * @Given /^(this customer) has an? ("[^"]+" based address) in their address book$/
     */
    public function thisCustomerHasAnAddressInAddressBook(CustomerInterface $customer, AddressInterface $address): void
    {
        $this->addAddressToCustomer($customer, $address);
    }

    private function addAddressToCustomer(CustomerInterface $customer, AddressInterface $address)
    {
        $customer->addAddress($address);

        $this->customerManager->flush();

        $this->sharedStorage->set('address_assigned_to_' . $customer->getFullName(), $address);
    }

    private function setDefaultAddressOfCustomer(CustomerInterface $customer, AddressInterface $address)
    {
        $customer->setDefaultAddress($address);

        $this->customerManager->flush();
    }
}
