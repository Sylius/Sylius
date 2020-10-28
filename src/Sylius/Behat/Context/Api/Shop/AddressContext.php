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

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\Converter\AdminToShopIriConverterInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Webmozart\Assert\Assert;

final class AddressContext implements Context
{
    /** @var ApiClientInterface */
    private $addressClient;

    /** @var ApiClientInterface */
    private $customerClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var AdminToShopIriConverterInterface */
    private $adminToShopIriConverter;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        ApiClientInterface $addressClient,
        ApiClientInterface $customerClient,
        ResponseCheckerInterface $responseChecker,
        AdminToShopIriConverterInterface $adminToShopIriConverter,
        SharedStorageInterface $sharedStorage
    ) {
        $this->addressClient = $addressClient;
        $this->customerClient = $customerClient;
        $this->responseChecker = $responseChecker;
        $this->adminToShopIriConverter = $adminToShopIriConverter;
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
     * @When I add it
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
    public function iTryToEditTheAddressOf(AddressInterface $address)
    {
        $this->addressClient->buildUpdateRequest((string) $address->getId());
    }

    /**
     * @When I change the first name to :firstName
     */
    public function iChangeTheFirstNameTo(string $firstName)
    {
        $this->addressClient->addRequestData('firstName', $firstName);
    }

    /**
     * @When I change the last name to :lastName
     */
    public function iChangeTheLastNameTo(string $lastName)
    {
        $this->addressClient->addRequestData('lastName', $lastName);
    }

    /**
     * @When I change the street to :street
     */
    public function iChangeTheStreetTo(string $street)
    {
        $this->addressClient->addRequestData('street', $street);
    }

    /**
     * @When I change the city to :city
     */
    public function iChangeTheCityTo(string $city)
    {
        $this->addressClient->addRequestData('city', $city);
    }

    /**
     * @When I change the postcode to :postcode
     */
    public function iChangeThePostcodeTo(string $postcode)
    {
        $this->addressClient->addRequestData('postcode', $postcode);
    }

    /**
     * @Then it should contain :value
     */
    public function itShouldContain(string $value)
    {
        Assert::true(
            $this->containsValue(
                $this->responseChecker->getResponseContent($this->addressClient->getLastResponse())['hydra:member'][0],
                $value
            )
        );
    }

    /**
     * @Then I should be notified that the address has been successfully updated
     */
    public function iShouldBeNotifiedThatTheAddressHasBeenSuccessfullyUpdated()
    {
        Assert::true($this->responseChecker->isUpdateSuccessful($this->addressClient->getLastResponse()));
    }

    /**
     * @Then I should be unable to edit their address
     */
    public function iShouldBeUnableToEditTheirAddress()
    {
        Assert::false($this->responseChecker->isUpdateSuccessful($this->addressClient->update()));
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

        $addressResponse = $this->addressClient->showByIri($this->adminToShopIriConverter->convert(
            $this->responseChecker->getValue($customerResponse, 'defaultAddress')
        ));

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

        Assert::same(sizeof($response['violations']), $expectedCount);
    }

    /**
     * @Then I should be notified that the province needs to be specified
     */
    public function iShouldBeNotifiedThatTheProvinceNeedsToBeSpecified(): void
    {
        $response = $this->responseChecker->getResponseContent($this->addressClient->getLastResponse());

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
        foreach ($addresses as $address){
            if ($firstName === $address['firstName'] && $lastName === $address['lastName']) {
                $addressIriArray = explode('/', $address['@id']);
                return end($addressIriArray);
            }
        }

        return null;
    }

    private function containsValue(array $data, string $value): bool
    {
        foreach ($data as $key=>$dataValue) {
            if ($dataValue === $value) return true;
        }
        return false;
    }

    private function getAddressIriFromAddressBookByFullName(string $fullName): ?string
    {
        Assert::notNull($fullName);
        [$firstName, $lastName] = explode(' ', $fullName);

        $addresses = $this->responseChecker->getCollection($this->addressClient->index());
        /** @var AddressInterface $address */
        foreach ($addresses as $address){
            if ($firstName === $address['firstName'] && $lastName === $address['lastName']) {
                return $address['@id'];
            }
        }

        return null;
    }
}
