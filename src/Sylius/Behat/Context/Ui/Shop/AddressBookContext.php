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
use Sylius\Behat\Page\UnexpectedPageException;
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
     * @Given /^I am editing the address of "([^"]+)"$/
     */
    public function iEditAddressOf($fullName)
    {
        $this->sharedStorage->set('full_name', $fullName);

        $this->addressBookIndexPage->open();
        $this->addressBookIndexPage->editAddress($fullName);
    }

    /**
     * @Given my default address is of :fullName
     * @When I set the address of :fullName as default
     */
    public function iSetTheAddressOfAsDefault($fullName)
    {
        $this->sharedStorage->set('full_name', $fullName);

        $this->addressBookIndexPage->setAsDefault($fullName);
    }

    /**
     * @Given I want to add a new address to my address book
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
     */
    public function iChangeMyTo($field, $value)
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
    public function iTryToEdit($fullName)
    {
        $this->sharedStorage->set('full_name', $fullName);

        $address = $this->getAddressOf($fullName);

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
     * @Then there should( still) be a single address in my book
     */
    public function iShouldSeeASingleAddressInTheList()
    {
        $this->assertAddressesCountOnPage(1);
    }

    /**
     * @Then this address should be assigned to :fullName
     * @Then /^the address assigned to "([^"]+)" should (appear|be) in my book$/
     */
    public function thisAddressShouldHavePersonFirstNameAndLastName($fullName)
    {
        Assert::true(
            $this->addressBookIndexPage->hasAddressOf($fullName),
            sprintf('An address of "%s" should be on the list.', $fullName)
        );
    }

    /**
     * @Then I should still be on the address addition page
     */
    public function iShouldStillBeOnAddressAdditionPage()
    {
        Assert::true(
            $this->addressBookCreatePage->isOpen(),
            'The address creation page should be opened.'
        );
    }

    /**
     * @Then I should be notified that the province needs to be specified
     */
    public function iShouldBeNotifiedThatTheProvinceNeedsToBeSpecified()
    {
        Assert::true(
            $this->addressBookCreatePage->hasProvinceValidationMessage(),
            'Province validation messages should be visible.'
        );
    }

    /**
     * @Then /^I should be notified about (\d+) errors$/
     */
    public function iShouldBeNotifiedAboutErrors($expectedCount)
    {
        $actualCount = $this->addressBookCreatePage->countValidationMessages();

        Assert::same(
            (int) $expectedCount,
            $actualCount,
            sprintf('There should be %d validation messages, but %d has been found.', $expectedCount, $actualCount)
        );
    }

    /**
     * @Then there should be no addresses
     */
    public function thereShouldBeNoAddresses()
    {
        Assert::true(
            $this->addressBookIndexPage->hasNoAddresses(),
            'There should be no addresses on the list.'
        );
    }

    /**
     * @Then I should not see the address assigned to :fullName
     */
    public function iShouldNotSeeAddressOf($fullName)
    {
        Assert::false(
            $this->addressBookIndexPage->hasAddressOf($fullName),
            sprintf('The address of "%s" should not be on the list.', $fullName)
        );
    }

    /**
     * @Then /^I should(?:| still) have (\d+) address(?:|es) in my address book$/
     */
    public function iShouldHaveAddresses($count)
    {
        $this->addressBookIndexPage->open();

        $this->assertAddressesCountOnPage((int) $count);
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

        Assert::false(
            $this->addressBookUpdatePage->isOpen(['id' => $address->getId()]),
            sprintf(
                'I should be unable to edit the address of "%s %s"',
                $address->getFirstName(),
                $address->getLastName()
            )
        );
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
        Assert::true(
            $this->addressBookIndexPage->hasNoDefaultAddress(),
            'There should be no default address.'
        );
    }

    /**
     * @Then /^the address of "([^"]+)" should be marked as my default$/
     * @Then /^(it) should be marked as my default address$/
     */
    public function itShouldBeMarkedAsMyDefaultAddress($fullName)
    {
        $actualFullName = $this->addressBookIndexPage->getFullNameOfDefaultAddress();

        Assert::same(
            $fullName,
            $actualFullName,
            sprintf('The default address should be of "%s", but is of "%s".', $fullName, $actualFullName)
        );
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

    /**
     * @param int $expectedCount
     *
     * @throws \InvalidArgumentException
     */
    private function assertAddressesCountOnPage($expectedCount)
    {
        $actualCount = $this->addressBookIndexPage->getAddressesCount();

        Assert::same(
            $expectedCount,
            $actualCount,
            sprintf(
                'There should be %d addresses on the list, but %d addresses has been found.',
                $expectedCount,
                $actualCount
            )
        );
    }
}
