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
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Webmozart\Assert\Assert;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class AddressBookContext implements Context
{
    /**
     * @var IndexPageInterface
     */
    private $addressBookIndexPage;

    /**
     * @var CreatePageInterface
     */
    private $addressBookCreatePage;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param IndexPageInterface $addressBookIndexPage
     * @param CreatePageInterface $addressBookCreatePage
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        IndexPageInterface $addressBookIndexPage,
        CreatePageInterface $addressBookCreatePage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->addressBookIndexPage = $addressBookIndexPage;
        $this->addressBookCreatePage = $addressBookCreatePage;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @When I browse my address book
     */
    public function iBrowseMyAddresses()
    {
        $this->addressBookIndexPage->open();
    }

    /**
     * @Given I want to add a new address to my address book
     */
    public function iWantToAddANewAddressToMyAddressBook()
    {
        $this->addressBookCreatePage->open();
    }

    /**
     * @When /^I specify the (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)")$/
     */
    public function iSpecifyItsDataAs(AddressInterface $address)
    {
        $this->addressBookCreatePage->fillAddressData($address);
    }

    /**
     * @When I add it
     */
    public function iAddIt()
    {
        $this->addressBookCreatePage->saveAddress();
    }

    /**
     * @When I delete the :fullName address
     */
    public function iDeleteTheAddress($fullname)
    {
        $this->addressBookIndexPage->deleteAddress($fullname);
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedAboutSuccessfulDelete()
    {
        $this->notificationChecker->checkNotification('Address has been successfully deleted.', NotificationType::success());
    }

    /**
     * @Then I should see a single address in my book
     */
    public function iShouldSeeASingleAddressInTheList()
    {
        $this->assertAddressesCountOnPage(1);
    }

    /**
     * @Then this address should be assigned to :fullName
     * @Then the address assigned to :fullName should appear in my book
     */
    public function thisAddressShouldHavePersonFirstNameAndLastName($fullName)
    {
        Assert::true(
            $this->addressBookIndexPage->hasAddressOf($fullName),
            sprintf('An address of "%s" should be on the list.', $fullName)
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
     * @Then I should be notified that it has been successfully added
     */
    public function iShouldBeNotifiedThatAddressHasBeenSuccessfullyAdded()
    {
        $this->notificationChecker->checkNotification('has been successfully added.', NotificationType::success());
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
