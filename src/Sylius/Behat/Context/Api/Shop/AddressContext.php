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
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Webmozart\Assert\Assert;

final class AddressContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var CountryNameConverterInterface */
    private $countryNameConverter;

    /** @var AddressRepositoryInterface */
    private $addressRepository;

    /** @var IriConverterInterface */
    private $iriConverter;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage,
        AddressRepositoryInterface $addressRepository,
        CountryNameConverterInterface $countryNameConverter,
        IriConverterInterface $iriConverter
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
        $this->addressRepository = $addressRepository;
        $this->countryNameConverter = $countryNameConverter;
        $this->iriConverter = $iriConverter;
    }

    /**
     * @Given I am editing the address of :fullName
     */
    public function iAmEditingTheAddressOf(string $fullName): void
    {
        $address = $this->getAddressOf($fullName);

        $this->client->buildUpdateRequest((string) $address->getId());
    }

    /**
     * @When I want to add a new address to my address book
     */
    public function iWantToAddANewAddressToMyAddressBook(): void
    {
        $this->client->buildCreateRequest();
    }

    /**
     * @When /^I specify the (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)")$/
     */
    public function iSpecifyTheAddressAs(AddressInterface $address): void
    {
        $this->client->setRequestData(
            [
                'countryCode' => $address->getCountryCode(),
                'street' => $address->getStreet(),
                'city' => $address->getCity(),
                'postcode' => $address->getPostcode(),
                'provinceName' => $address->getProvinceName(),
                'firstName' => $address->getFirstName(),
                'lastName' => $address->getLastName(),
            ]
        );
    }

    /**
     * @When I add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I leave every field empty
     */
    public function iLeaveEveryFieldEmpty(): void
    {
        $this->client->setRequestData([]);
    }

    /**
     * @When I choose :countryName as my country
     */
    public function iChooseAsMyCountry(string $countryName): void
    {
        $countryCode = $this->countryNameConverter->convertToCode($countryName);

        $this->client->addRequestData('countryCode', $countryCode);
    }

    /**
     * @When I do not specify province
     */
    public function iDoNotSpecifyProvince(): void
    {
        $this->client->addRequestData('provinceName', '');
        $this->client->addRequestData('provinceCode', '');
    }

    /**
     * @When I remove the street
     */
    public function iRemoveTheStreet(): void
    {
        $this->client->addRequestData('street', null);
    }

    /**
     * @When I save my changed address
     */
    public function iSaveMyChangedAddress(): void
    {
        $this->client->update();
    }

    /**
     * @When I browse my address book
     */
    public function iBrowseMyAddresses(): void
    {
        $this->client->index();
    }

    /**
     * @When I delete the :fullName address
     */
    public function iDeleteTheAddress(string $fullName): void
    {
        $id = $this->getAddressIdFromAddressBookByFullName($fullName);

        $this->client->delete($id);
    }

    /**
     * @Then /^I should(?:| still) have a single address in my address book$/
     * @Then /^I should(?:| still) have (\d+) addresses in my address book$/
     */
    public function iShouldHaveAddresses(int $count = 1): void
    {
        $this->client->index();

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
     * @Then I should be notified that the address has been successfully deleted
     */
    public function iShouldBeNotifiedThatAddressHasBeenDeleted(): void
    {
        Assert::true($this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()));
    }

    /**
     * @Then I should be notified that the address has been successfully added
     */
    public function iShouldBeNotifiedThatTheAddressHasBeenSuccessfullyAdded(): void
    {
        Assert::true($this->responseChecker->isCreationSuccessful($this->client->getLastResponse()));
    }

    /**
     * @Then /^(address "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+"(?:|, "[^"]+")) should(?:| still) be marked as my default address$/
     */
    public function addressShouldBeMarkedAsMyDefaultAddress(AddressInterface $address): void
    {
        $response = $this->responseChecker->getResponseContent($this->client->getLastResponse());

        /** @var CustomerInterface $customer */
        $customer = $this->iriConverter->getItemFromIri($response['customer']);

        /** @var AddressInterface $defaultAddress */
        $defaultAddress = $customer->getDefaultAddress();

        Assert::same($address->getCity(), $defaultAddress->getCity());
        Assert::same($address->getStreet(), $defaultAddress->getStreet());
        Assert::same($address->getCountryCode(), $defaultAddress->getCountryCode());
        Assert::same($address->getPostcode(), $defaultAddress->getPostcode());
        Assert::same($address->getProvinceCode(), $defaultAddress->getProvinceCode());
        Assert::same($address->getProvinceName(), $defaultAddress->getProvinceName());
    }

    /**
     * @Then I should still be on the address addition page
     */
    public function iShouldStillBeOnTheAddressAdditionPage(): void
    {
        // Intentionally left empty
    }

    /**
     *  @Then I should be notified about :expectedCount errors
     */
    public function iShouldBeNotifiedAboutErrors(int $expectedCount): void
    {
        $response = $this->responseChecker->getResponseContent($this->client->getLastResponse());

        Assert::same(sizeof($response['violations']), $expectedCount);
    }

    /**
     * @Then I should be notified that the province needs to be specified
     */
    public function iShouldBeNotifiedThatTheProvinceNeedsToBeSpecified(): void
    {
        $response = $this->responseChecker->getResponseContent($this->client->getLastResponse());

        Assert::inArray(['propertyPath' => '', 'message' => 'Please select proper province.'], $response['violations']);
    }

    /**
     * @Then I should still be on the :fullName address edit page
     */
    public function iShouldStillBeOnTheAddressEditPage(string $fullName): void
    {
        // Intentionally left empty
    }

    /**
     * @Then I should still have :provinceName as my specified province
     * @Then I should still have :provinceName as my chosen province
     */
    public function iShouldStillHaveAsMySpecifiedProvince(string $provinceName): void
    {
        Assert::false($this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()));
    }

    private function getAddressOf(string $fullName): AddressInterface
    {
        [$firstName, $lastName] = explode(' ', $fullName);

        /** @var AddressInterface $address */
        $address = $this->addressRepository->findOneBy(['firstName' => $firstName, 'lastName' => $lastName]);
        Assert::notNull($address);

        return $address;
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
                $addressIriArray = explode('/', $address['@id']);
                return end($addressIriArray);
            }
        }

        return null;
    }
}
