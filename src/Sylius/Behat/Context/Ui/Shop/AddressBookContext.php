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

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Shop\Account\AddressBook\CreatePageInterface;
use Sylius\Behat\Page\Shop\Account\AddressBook\IndexPageInterface;
use Sylius\Behat\Page\Shop\Account\AddressBook\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class AddressBookContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private RepositoryInterface $addressRepository,
        private IndexPageInterface $addressBookIndexPage,
        private CreatePageInterface $addressBookCreatePage,
        private UpdatePageInterface $addressBookUpdatePage,
        private CurrentPageResolverInterface $currentPageResolver,
        private NotificationCheckerInterface $notificationChecker,
    ) {
    }

    /**
     * @Given I am editing the address of :fullName
     * @When I want to edit the address of :fullName
     */
    public function iEditAddressOf(string $fullName): void
    {
        $this->sharedStorage->set('full_name', $fullName);

        $this->addressBookIndexPage->open();
        $this->addressBookIndexPage->editAddress($fullName);
    }

    /**
     * @When I set the address of :fullName as default
     */
    public function iSetTheAddressOfAsDefault($fullName)
    {
        $this->sharedStorage->set('full_name', $fullName);

        $this->addressBookIndexPage->setAsDefault($fullName);
    }

    /**
     * @When I want to add a new address to my address book
     */
    public function iWantToAddANewAddressToMyAddressBook()
    {
        $this->addressBookCreatePage->open();
    }

    /**
     * @Given I am browsing my address book
     * @When I browse my address book
     */
    public function iBrowseMyAddresses()
    {
        $this->addressBookIndexPage->open();
    }

    /**
     * @When I specify :provinceName as my province
     */
    public function iSpecifyAsMyProvince($provinceName)
    {
        $this->addressBookUpdatePage->specifyProvince($provinceName);
    }

    /**
     * @When I choose :provinceName as my province
     */
    public function iChooseAsMyProvince($provinceName)
    {
        $this->addressBookUpdatePage->selectProvince($provinceName);
    }

    /**
     * @When I choose :countryName as my country
     */
    public function iChooseAsMyCountry($countryName)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->getCurrentPage();
        $currentPage->selectCountry($countryName);
    }

    /**
     * @When /^I change the ([^"]+) to "([^"]+)"$/
     * @When /^I remove the ([^"]+)$/
     */
    public function iChangeMyTo($field, $value = null)
    {
        $this->addressBookUpdatePage->fillField($field, $value);
    }

    /**
     * @When /^I specify the (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)")$/
     */
    public function iSpecifyTheAddressAs(AddressInterface $address)
    {
        $this->addressBookCreatePage->fillAddressData($address);
    }

    /**
     * @When I leave every field empty
     */
    public function iLeaveEveryFieldEmpty()
    {
        // Intentionally left empty
    }

    /**
     * @When I do not specify province
     */
    public function iDoNotSpecifyProvince(): void
    {
        // Intentionally left empty
    }

    /**
     * @When I add it
     */
    public function iAddIt()
    {
        $this->addressBookCreatePage->addAddress();
    }

    /**
     * @When I save my changed address
     */
    public function iSaveChangedAddress()
    {
        $this->addressBookUpdatePage->saveChanges();
    }

    /**
     * @When I delete the :fullName address
     */
    public function iDeleteTheAddress($fullname)
    {
        $this->addressBookIndexPage->deleteAddress($fullname);
    }

    /**
     * @When /^I try to edit the address of "([^"]+)"$/
     */
    public function iTryToEditTheAddressOf($fullName)
    {
        $address = $this->getAddressOf($fullName);

        $this->sharedStorage->set('full_name', sprintf('%s %s', $address->getFirstName(), $address->getLastName()));

        $this->addressBookUpdatePage->tryToOpen(['id' => $address->getId()]);
    }

    /**
     * @Then /^it should contain "([^"]+)"$/
     */
    public function itShouldContain($value)
    {
        $fullName = $this->sharedStorage->get('full_name');

        Assert::true($this->addressBookIndexPage->addressOfContains($fullName, $value));
    }

    /**
     * @Then it should contain country :countryName
     */
    public function itShouldContainCountry(string $countryName): void
    {
        $fullName = $this->sharedStorage->get('full_name');

        Assert::true($this->addressBookIndexPage->addressOfContains($fullName, strtoupper($countryName)));
    }

    /**
     * @Then it should contain province :provinceName
     */
    public function itShouldContainProvince(string $provinceName): void
    {
        $fullName = $this->sharedStorage->get('full_name');

        Assert::true($this->addressBookIndexPage->addressOfContains($fullName, $provinceName));
    }

    /**
     * @Then this address should be assigned to :fullName
     * @Then /^the address assigned to "([^"]+)" should (appear|be) in my book$/
     */
    public function thisAddressShouldBeAssignedTo(string $fullName): void
    {
        Assert::true($this->addressBookIndexPage->hasAddressOf($fullName));
    }

    /**
     * @Then I should still be on the address addition page
     */
    public function iShouldStillBeOnAddressAdditionPage()
    {
        $this->addressBookCreatePage->verify();
    }

    /**
     * @Then I should still be on the :fullName address edit page
     */
    public function iShouldStillBeOnTheAddressEditPage($fullName)
    {
        $address = $this->getAddressOf($fullName);

        Assert::true($this->addressBookUpdatePage->isOpen(['id' => $address->getId()]));
    }

    /**
     * @Then I should still have :value as my specified province
     */
    public function iShouldStillHaveAsMySpecifiedProvince($value)
    {
        Assert::same($this->addressBookUpdatePage->getSpecifiedProvince(), $value);
    }

    /**
     * @Then I should still have :value as my chosen province
     */
    public function iShouldStillHaveAsMyChosenProvince($value)
    {
        Assert::same($this->addressBookUpdatePage->getSelectedProvince(), $value);
    }

    /**
     * @Then I should be notified that the province needs to be specified
     */
    public function iShouldBeNotifiedThatTheProvinceNeedsToBeSpecified()
    {
        Assert::true($this->addressBookCreatePage->hasProvinceValidationMessage());
    }

    /**
     * @Then /^I should be notified about (\d+) errors$/
     */
    public function iShouldBeNotifiedAboutErrors(int $expectedCount): void
    {
        Assert::same($this->addressBookCreatePage->countValidationMessages(), $expectedCount);
    }

    /**
     * @Then there should be no addresses
     */
    public function thereShouldBeNoAddresses()
    {
        Assert::true($this->addressBookIndexPage->hasNoAddresses());
    }

    /**
     * @Then I should not see the address assigned to :fullName
     */
    public function iShouldNotSeeAddressOf($fullName)
    {
        Assert::false($this->addressBookIndexPage->hasAddressOf($fullName));
    }

    /**
     * @Then /^I should(?:| still) have a single address in my address book$/
     * @Then /^I should(?:| still) have (\d+) addresses in my address book$/
     */
    public function iShouldHaveAddresses($count = 1)
    {
        $this->addressBookIndexPage->open();

        Assert::same($this->addressBookIndexPage->getAddressesCount(), (int) $count);
    }

    /**
     * @Then I should be notified that the address has been successfully added
     */
    public function iShouldBeNotifiedThatAddressHasBeenSuccessfullyAdded()
    {
        $this->notificationChecker->checkNotification('Address has been successfully added.', NotificationType::success());
    }

    /**
     * @Then I should be notified that the address has been successfully deleted
     */
    public function iShouldBeNotifiedAboutSuccessfulDelete()
    {
        $this->notificationChecker->checkNotification('Address has been successfully deleted.', NotificationType::success());
    }

    /**
     * @Then I should be unable to edit their address
     */
    public function iShouldBeUnableToEditTheirAddress()
    {
        $address = $this->getAddressOf($this->sharedStorage->getLatestResource());

        Assert::false($this->addressBookUpdatePage->isOpen(['id' => $address->getId()]));
    }

    /**
     * @Then I should be notified that the address has been successfully updated
     */
    public function iShouldBeNotifiedAboutSuccessfulUpdate()
    {
        $this->notificationChecker->checkNotification('Address has been successfully updated.', NotificationType::success());
    }

    /**
     * @Then I should be notified that the address has been set as default
     */
    public function iShouldBeNotifiedThatAddressHasBeenSetAsDefault()
    {
        $this->notificationChecker->checkNotification('Address has been set as default', NotificationType::success());
    }

    /**
     * @Then I should not have a default address
     */
    public function iShouldHaveNoDefaultAddress()
    {
        Assert::true($this->addressBookIndexPage->hasNoDefaultAddress());
    }

    /**
     * @Then /^(address "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+"(?:|, "[^"]+")) should(?:| still) be marked as my default address$/
     * @Then /^(address "[^"]+", "[^"]+", "[^"]+", "[^"]+", "[^"]+"(?:|, "[^"]+")) should(?:| still) be set as my default address$/
     */
    public function addressShouldBeMarkedAsMyDefaultAddress(AddressInterface $address)
    {
        $actualFullName = $this->addressBookIndexPage->getFullNameOfDefaultAddress();
        $expectedFullName = sprintf('%s %s', $address->getFirstName(), $address->getLastName());

        Assert::same($actualFullName, $expectedFullName);
    }

    /**
     * @Then I should be able to update it without unexpected alert
     */
    public function iShouldBeAbleToUpdateItWithoutUnexpectedAlert(): void
    {
        $this->addressBookUpdatePage->waitForFormToStopLoading();
    }

    /**
     * @param string $fullName
     *
     * @return AddressInterface
     */
    private function getAddressOf($fullName)
    {
        [$firstName, $lastName] = explode(' ', $fullName);

        /** @var AddressInterface $address */
        $address = $this->addressRepository->findOneBy(['firstName' => $firstName, 'lastName' => $lastName]);
        Assert::notNull($address);

        return $address;
    }

    /**
     * @return SymfonyPageInterface
     */
    private function getCurrentPage()
    {
        return $this
            ->currentPageResolver
            ->getCurrentPageWithForm([
                $this->addressBookCreatePage,
                $this->addressBookUpdatePage,
        ])
        ;
    }
}
