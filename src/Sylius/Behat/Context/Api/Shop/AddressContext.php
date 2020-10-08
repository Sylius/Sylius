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

namespace Sylius\Behat\Context\Api\Shop;

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Webmozart\Assert\Assert;

final class AddressContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I browse my address book
     */
    public function iBrowseMyAddresses(): void
    {
        $this->client->index();
    }

    /**
     * @Then /^I should(?:| still) have a single address in my address book$/
     * @Then /^I should(?:| still) have (\d+) addresses in my address book$/
     */
    public function iShouldHaveAddresses(int $count = 1): void
    {
        Assert::same(count($this->responseChecker->getCollection($this->client->getLastResponse())), $count);
    }

    /**
     * @Then this address should be assigned to :fullName
     */
    public function thisAddressShouldBeAssignedTo(string $fullName): void
    {
        /** @var AddressInterface $address */
        $address = $this->sharedStorage->get('address_assigned_to_' . $fullName);

        $addressBook = $this->responseChecker->getCollection($this->client->getLastResponse());

        Assert::true($this->addressBookHasAddress($addressBook, $address));
    }

    /**
     * @Then I should not see the address assigned to :fullName
     */
    public function iShouldNotSeeTheAddressAssignedTo(string $fullName): void
    {
        /** @var AddressInterface $address */
        $address = $this->sharedStorage->get('address_assigned_to_' . $fullName);

        $addressBook = $this->responseChecker->getCollection($this->client->getLastResponse());

        Assert::false($this->addressBookHasAddress($addressBook, $address));
    }

    /**
     * @Then there should be no addresses
     */
    public function thereShouldBeNoAddresses(): void
    {
        $this->client->index();

        Assert::same(count($this->responseChecker->getCollection($this->client->getLastResponse())), 0);
    }

    /**
     * @Then I delete the :fullName address
     */
    public function iDeleteTheAddress(string $fullName): void
    {
        $id = $this->getAddressIdFromAddressBookByFullName($fullName);

        $this->client->delete($id);
    }

    /**
     * @Then I should be notified that the address has been successfully deleted
     */
    public function iShouldBeNotifiedThatAddressHasBeenDeleted(): void
    {
        Assert::true($this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()));
    }

    private function addressBookHasAddress(array $addressBook, AddressInterface $addressToCompare): bool
    {
        foreach ($addressBook as $address) {
            if (
                $address['firstName'] === $addressToCompare->getFirstName() &&
                $address['lastName'] === $addressToCompare->getLastName() &&
                $address['countryCode'] === $addressToCompare->getCountryCode() &&
                $address['street'] === $addressToCompare->getStreet() &&
                $address['city'] === $addressToCompare->getCity() &&
                $address['postcode'] === $addressToCompare->getPostcode() &&
                $address['provinceName'] === $addressToCompare->getProvinceName() &&
                $address['provinceCode'] === $addressToCompare->getProvinceCode()
            ) {
                return true;
            }
        }

        return false;
    }

    private function getAddressIdFromAddressBookByFullName(string $fullName): ?string
    {
        Assert::notNull($fullName);
        $fullNameArray = explode(' ',$fullName);

        $addresses = $this->responseChecker->getCollection($this->client->getLastResponse());
        /** @var AddressInterface $address */
        foreach ($addresses as $address){
            if ($fullNameArray[0] === $address['firstName'] && $fullNameArray[1] === $address['lastName']) {
                $addressIriStringArray = explode('/', $address['@id']);
                return end($addressIriStringArray);
            }
        }
    }
}
