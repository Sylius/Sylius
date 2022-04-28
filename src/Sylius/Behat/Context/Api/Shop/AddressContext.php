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
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Webmozart\Assert\Assert;

final class AddressContext implements Context
{
    private ApiClientInterface $addressClient;

    private ApiClientInterface $customerClient;

    private ResponseCheckerInterface $responseChecker;

    private IriConverterInterface $iriConverter;

    private SharedStorageInterface $sharedStorage;

    public function __construct(
        ApiClientInterface $addressClient,
        ApiClientInterface $customerClient,
        ResponseCheckerInterface $responseChecker,
        IriConverterInterface $iriConverter,
        SharedStorageInterface $sharedStorage
    ) {
        $this->addressClient = $addressClient;
        $this->customerClient = $customerClient;
        $this->responseChecker = $responseChecker;
        $this->iriConverter = $iriConverter;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given /^I am editing the (address of "([^"]+)")$/
     */
    public function iAmEditingTheAddressOf(AddressInterface $address): void
    {
        $this->addressClient->buildUpdateRequest((string) $address->getId());
    }

    /**
     * @When I want to add a new address to my address book
     */
    public function iWantToAddANewAddressToMyAddressBook(): void
    {
        $this->addressClient->buildCreateRequest();
    }

    /**
     * @When /^I specify the (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)")$/
     */
    public function iSpecifyTheAddressAs(AddressInterface $address): void
    {
        $this->addressClient->setRequestData([
            'countryCode' => $address->getCountryCode(),
            'street' => $address->getStreet(),
            'city' => $address->getCity(),
            'postcode' => $address->getPostcode(),
            'provinceName' => $address->getProvinceName(),
            'firstName' => $address->getFirstName(),
            'lastName' => $address->getLastName(),
        ]);
    }

    /**
     * @When I (try to) add it
     */
    public function iAddIt(): void
    {
        $this->addressClient->create();
    }

    /**
     * @When I leave every field empty
     */
    public function iLeaveEveryFieldEmpty(): void
    {
        $this->addressClient->setRequestData([]);
    }

    /**
     * @When I choose :countryCode as my country
     */
    public function iChooseAsMyCountry(string $countryCode): void
    {
        $this->addressClient->addRequestData('countryCode', $countryCode);
    }

    /**
     * @When I do not specify province
     */
    public function iDoNotSpecifyProvince(): void
    {
        $this->addressClient->addRequestData('provinceName', '');
        $this->addressClient->addRequestData('provinceCode', '');
    }

    /**
     * @When I remove the street
     */
    public function iRemoveTheStreet(): void
    {
        $this->addressClient->addRequestData('street', null);
    }

    /**
     * @When I save my changed address
     */
    public function iSaveMyChangedAddress(): void
    {
        $this->addressClient->update();
    }

    /**
     * @When I browse my address book
     */
    public function iBrowseMyAddresses(): void
    {
        $this->addressClient->index();
    }

    /**
     * @When I delete the :fullName address
     */
    public function iDeleteTheAddress(string $fullName): void
    {
        $id = $this->getAddressIdFromAddressBookByFullName($fullName);

        $this->addressClient->delete($id);
    }

    /**
     * @When /^I try to delete (address belongs to "([^"]+)")$/
     */
    public function iDeleteTheAddressBelongsTo(AddressInterface $address): void
    {
        $this->addressClient->delete((string) $address->getId());
    }

    /**
     * @When I set the address of :fullName as default
     */
    public function iSetTheAddressOfAsDefault(string $fullName): void
    {
        $addressIri = $this->getAddressIriFromAddressBookByFullName($fullName);

        $this->customerClient->buildUpdateRequest((string) $this->sharedStorage->get('user')->getCustomer()->getId());
        $this->customerClient->addRequestData('defaultAddress', $addressIri);
        $this->customerClient->update();
    }

    /**
     * @When /^I try to edit the (address of "([^"]+)")$/
     */
    public function iTryToEditTheAddressOf(AddressInterface $address): void
    {
        $this->addressClient->buildUpdateRequest((string) $address->getId());
    }

    /**
     * @When I change the first name to :firstName
     */
    public function iChangeTheFirstNameTo(string $firstName): void
    {
        $this->addressClient->addRequestData('firstName', $firstName);
    }

    /**
     * @When I change the last name to :lastName
     */
    public function iChangeTheLastNameTo(string $lastName): void
    {
        $this->addressClient->addRequestData('lastName', $lastName);
    }

    /**
     * @When I change the street to :street
     */
    public function iChangeTheStreetTo(string $street): void
    {
        $this->addressClient->addRequestData('street', $street);
    }

    /**
     * @When I change the city to :city
     */
    public function iChangeTheCityTo(string $city): void
    {
        $this->addressClient->addRequestData('city', $city);
    }

    /**
     * @When I change the postcode to :postcode
     */
    public function iChangeThePostcodeTo(string $postcode): void
    {
        $this->addressClient->addRequestData('postcode', $postcode);
    }

    /**
     * @When I choose :province as my province
     */
    public function iChooseAsMyProvince(ProvinceInterface $province): void
    {
        $this->addressClient->addRequestData('provinceCode', $province->getCode());
    }

    /**
     * @When I specify :provinceName as my province
     */
    public function iSpecifyProvince(string $provinceName): void
    {
        $this->addressClient->addRequestData('provinceName', $provinceName);
    }

    /**
     * @Then it should contain country :countryCode
     */
    public function itShouldContainCountry(string $countryCode): void
    {
        $this->itShouldContain($countryCode);
    }

    /**
     * @Then it should contain province :province
     */
    public function itShouldContainProvince(ProvinceInterface $province): void
    {
        $this->itShouldContain($province->getCode());
    }

    /**
     * @Then it should contain :value
     */
    public function itShouldContain(string $value): void
    {
        Assert::true(
            $this->containsValue(
                $this->responseChecker->getCollection($this->addressClient->getLastResponse())[0],
                $value
            )
        );
    }

    /**
     * @Then I should be notified that the address has been successfully updated
     */
    public function iShouldBeNotifiedThatTheAddressHasBeenSuccessfullyUpdated(): void
    {
        Assert::true($this->responseChecker->isUpdateSuccessful($this->addressClient->getLastResponse()));
    }

    /**
     * @Then I should be unable to edit their address
     */
    public function iShouldBeUnableToEditTheirAddress(): void
    {
        Assert::false($this->responseChecker->isUpdateSuccessful($this->addressClient->update()));
    }

    /**
     * @When /^I try to view details of (address belongs to "([^"]+)")$/
     */
    public function iTryToViewDetailsOfAddressBelongingTo(AddressInterface $address): void
    {
        $this->addressClient->showByIri($this->iriConverter->getIriFromItem($address));
    }

    /**
     * @Then /^I should(?:| still) have a single address in my address book$/
     * @Then /^I should(?:| still) have (\d+) addresses in my address book$/
     */
    public function iShouldHaveAddresses(int $count = 1): void
    {
        Assert::same(count($this->responseChecker->getCollection($this->addressClient->index())), $count);
    }

    /**
     * @Then this address should be assigned to :fullName
     * @Then the address assigned to :fullName should be in my book
     */
    public function thisAddressShouldBeAssignedTo(string $fullName): void
    {
        Assert::notNull(
            $this->getAddressIriFromAddressBookByFullName($fullName),
            sprintf('There is no address assigned to %s', $fullName)
        );
    }

    /**
     * @Then I should not see the address assigned to :fullName
     */
    public function iShouldNotSeeTheAddressAssignedTo(string $fullName): void
    {
        /** @var AddressInterface $address */
        $address = $this->sharedStorage->get('address_assigned_to_' . $fullName);

        $addressBook = $this->responseChecker->getCollection($this->addressClient->getLastResponse());

        Assert::false($this->addressBookHasAddress($addressBook, $address));
    }

    /**
     * @Then there should be no addresses
     */
    public function thereShouldBeNoAddresses(): void
    {
        Assert::same(count($this->responseChecker->getCollection($this->addressClient->index())), 0);
    }

    /**
     * @Then I should be notified that the address has been successfully deleted
     */
    public function iShouldBeNotifiedThatAddressHasBeenDeleted(): void
    {
        Assert::true($this->responseChecker->isDeletionSuccessful($this->addressClient->getLastResponse()));
    }

    /**
     * @Then I should be notified that the address has been successfully added
     */
    public function iShouldBeNotifiedThatTheAddressHasBeenSuccessfullyAdded(): void
    {
        Assert::true($this->responseChecker->isCreationSuccessful($this->addressClient->getLastResponse()));
    }

    /**
     * @Then /^(address "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+"(?:|, "[^"]+")) should(?:| still) be marked as my default address$/
     * @Then /^(address "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+"(?:|, "[^"]+")) should(?:| still) be set as my default address$/
     */
    public function addressShouldBeMarkedAsMyDefaultAddress(AddressInterface $address): void
    {
        $customerResponse = $this->customerClient->show((string) $this->sharedStorage->get('user')->getCustomer()->getId());
        $addressResponse = $this->addressClient->showByIri($this->responseChecker->getValue($customerResponse, 'defaultAddress'));

        Assert::true($this->responseChecker->hasValue($addressResponse, 'city', $address->getCity()));
        Assert::true($this->responseChecker->hasValue($addressResponse, 'street', $address->getStreet()));
        Assert::true($this->responseChecker->hasValue($addressResponse, 'countryCode', $address->getCountryCode()));
        Assert::true($this->responseChecker->hasValue($addressResponse, 'postcode', $address->getPostcode()));
        Assert::true($this->responseChecker->hasValue($addressResponse, 'provinceCode', $address->getProvinceCode()));
        Assert::true($this->responseChecker->hasValue($addressResponse, 'provinceName', $address->getProvinceName()));
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
        $response = $this->responseChecker->getResponseContent($this->addressClient->getLastResponse());

        Assert::same(count($response['violations']), $expectedCount);
    }

    /**
     * @Then I should be notified that the province needs to be specified
     */
    public function iShouldBeNotifiedThatTheProvinceNeedsToBeSpecified(): void
    {
        Assert::true(
            $this->responseChecker->hasViolationWithMessage(
                $this->addressClient->getLastResponse(),
                'Please select proper province.'
            )
        );
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
        Assert::false($this->responseChecker->isUpdateSuccessful($this->addressClient->getLastResponse()));
    }

    /**
     * @Then I should not have a default address
     */
    public function iShouldHaveNoDefaultAddress(): void
    {
        $userShowResponse = $this->customerClient->show((string) $this->sharedStorage->get('user')->getCustomer()->getId());
        Assert::null(
            $this->responseChecker->getValue($userShowResponse, 'defaultAddress'),
            'Default address should be null'
        );
    }

    /**
     * @Then I should be notified that the address has been set as default
     */
    public function iShouldBeNotifiedThatAddressHasBeenSetAsDefault(): void
    {
        Assert::true($this->responseChecker->isUpdateSuccessful($this->addressClient->getLastResponse()));
    }

    /**
     * @Then I should not see any details of address
     */
    public function iShouldNotSeeAnyDetailsOfAddress(): void
    {
        Assert::true($this->responseChecker->hasAccessDenied($this->addressClient->getLastResponse()));
    }

    /**
     * @Then I should not be able to add it
     */
    public function iShouldNotBeAbleToDoIt(): void
    {
        Assert::true($this->responseChecker->hasAccessDenied($this->addressClient->getLastResponse()));
    }

    /**
     * @Then I should not be able to delete it
     */
    public function iShouldNotBeAbleToDeleteIt(): void
    {
        Assert::true($this->responseChecker->hasAccessDenied($this->addressClient->getLastResponse()));
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
        [$firstName, $lastName] = explode(' ', $fullName);

        $addresses = $this->responseChecker->getCollection($this->addressClient->getLastResponse());
        /** @var AddressInterface $address */
        foreach ($addresses as $address) {
            if ($firstName === $address['firstName'] && $lastName === $address['lastName']) {
                return (string) $address['id'];
            }
        }

        return null;
    }

    private function containsValue(array $data, string $value): bool
    {
        foreach ($data as $key => $dataValue) {
            if ($dataValue === $value) {
                return true;
            }
        }

        return false;
    }

    private function getAddressIriFromAddressBookByFullName(string $fullName): ?string
    {
        Assert::notNull($fullName);
        [$firstName, $lastName] = explode(' ', $fullName);

        $addresses = $this->responseChecker->getCollection($this->addressClient->index());
        /** @var AddressInterface $address */
        foreach ($addresses as $address) {
            if ($firstName === $address['firstName'] && $lastName === $address['lastName']) {
                return $address['@id'];
            }
        }

        return null;
    }
}
