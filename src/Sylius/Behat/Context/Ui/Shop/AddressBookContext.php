<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Shop\Account\AddressBook\CreatePageInterface;
use Sylius\Behat\Page\Shop\Account\AddressBook\IndexPageInterface;
use Sylius\Behat\Page\Shop\Account\AddressBook\UpdatePageInterface;
use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class AddressBookContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var RepositoryInterface
     */
    private $addressRepository;

    /**
     * @var IndexPageInterface
     */
    private $addressBookIndexPage;

    /**
     * @var CreatePageInterface
     */
    private $addressBookCreatePage;

    /**
     * @var UpdatePageInterface
     */
    private $addressBookUpdatePage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param RepositoryInterface $addressRepository
     * @param IndexPageInterface $addressBookIndexPage
     * @param CreatePageInterface $addressBookCreatePage
     * @param UpdatePageInterface $addressBookUpdatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $addressRepository,
        IndexPageInterface $addressBookIndexPage,
        CreatePageInterface $addressBookCreatePage,
        UpdatePageInterface $addressBookUpdatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->addressRepository = $addressRepository;
        $this->addressBookIndexPage = $addressBookIndexPage;
        $this->addressBookCreatePage = $addressBookCreatePage;
        $this->addressBookUpdatePage = $addressBookUpdatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given I am editing the address of :fullName
     */
    public function iEditAddressOf($fullName)
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

        $this->addressBookIndexPage->addressOfContains($fullName, $value);
    }

    /**
     * @Then this address should be assigned to :fullName
     * @Then /^the address assigned to "([^"]+)" should (appear|be) in my book$/
     */
    public function thisAddressShouldHavePersonFirstNameAndLastName($fullName)
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
    public function iShouldBeNotifiedAboutErrors($expectedCount)
    {
        Assert::same($this->addressBookCreatePage->countValidationMessages(), (int) $expectedCount);
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
     */
    public function addressShouldBeMarkedAsMyDefaultAddress(AddressInterface $address)
    {
        $actualFullName = $this->addressBookIndexPage->getFullNameOfDefaultAddress();
        $expectedFullName = sprintf('%s %s', $address->getFirstName(), $address->getLastName());

        Assert::same($actualFullName, $expectedFullName);
    }

    /**
     * @param string $fullName
     *
     * @return AddressInterface
     */
    private function getAddressOf($fullName)
    {
        list($firstName, $lastName) = explode(' ', $fullName);

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
                $this->addressBookUpdatePage
        ]);
    }
}
