<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\ShippingMethod\IndexPageInterface;
use Sylius\Behat\Page\Admin\ShippingMethod\CreatePageInterface;
use Sylius\Behat\Page\Admin\ShippingMethod\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ManagingShippingMethodsContext implements Context
{
    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @When I want to create a new shipping method
     */
    public function iWantToCreateANewShippingMethod()
    {
        $this->createPage->open();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null)
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I specify its position as :position
     */
    public function iSpecifyItsPositionAs($position = null)
    {
        $this->createPage->specifyPosition($position);
    }

    /**
     * @When I name it :name in :language
     * @When I rename it to :name in :language
     */
    public function iNameItIn($name, $language)
    {
        $this->createPage->nameIt($name, $language);
    }

    /**
     * @When I describe it as :description in :language
     */
    public function iDescribeItAsIn($description, $language)
    {
        $this->createPage->describeIt($description, $language);
    }

    /**
     * @When I define it for the :zoneName zone
     */
    public function iDefineItForTheZone($zoneName)
    {
        $this->createPage->chooseZone($zoneName);
    }

    /**
     * @When I specify its amount as :amount for :channel channel
     */
    public function iSpecifyItsAmountForChannel($amount, ChannelInterface $channel)
    {
        $this->createPage->specifyAmountForChannel($channel->getCode(), $amount);
    }

    /**
     * @When I make it available in channel :channelName
     */
    public function iMakeItAvailableInChannel($channelName)
    {
        $this->createPage->checkChannel($channelName);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @When I choose :calculatorName calculator
     * @When I do not specify amount for :calculatorName calculator
     */
    public function iChooseCalculator($calculatorName)
    {
        $this->createPage->chooseCalculator($calculatorName);
    }

    /**
     * @Then the shipping method :shipmentMethod should appear in the registry
     * @Then the shipping method :shipmentMethod should be in the registry
     */
    public function theShipmentMethodShouldAppearInTheRegistry($shipmentMethodName)
    {
        $this->iWantToBrowseShippingMethods();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $shipmentMethodName]));
    }

    /**
     * @Given /^(this shipping method) should still be in the registry$/
     */
    public function thisShippingMethodShouldStillBeInTheRegistry(ShippingMethodInterface $shippingMethod)
    {
        $this->theShipmentMethodShouldAppearInTheRegistry($shippingMethod->getName());
    }

    /**
     * @Then the shipping method :shippingMethod should be available in channel :channelName
     */
    public function theShippingMethodShouldBeAvailableInChannel(
        ShippingMethodInterface $shippingMethod,
        $channelName
    ) {
        $this->iWantToModifyAShippingMethod($shippingMethod);

        Assert::true($this->updatePage->isAvailableInChannel($channelName));
    }

    /**
     * @Then I should be notified that shipping method with this code already exists
     */
    public function iShouldBeNotifiedThatShippingMethodWithThisCodeAlreadyExists()
    {
        Assert::same($this->createPage->getValidationMessage('code'), 'The shipping method with given code already exists.');
    }

    /**
     * @Then there should still be only one shipping method with :element :code
     */
    public function thereShouldStillBeOnlyOneShippingMethodWith($element, $code)
    {
        $this->iWantToBrowseShippingMethods();

        Assert::true($this->indexPage->isSingleResourceOnPage([$element => $code]));
    }

    /**
     * @Given I want to modify a shipping method :shippingMethod
     * @Given /^I want to modify (this shipping method)$/
     */
    public function iWantToModifyAShippingMethod(ShippingMethodInterface $shippingMethod)
    {
        $this->updatePage->open(['id' => $shippingMethod->getId()]);
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @Then /^(this shipping method) name should be "([^"]+)"$/
     * @Then /^(this shipping method) should still be named "([^"]+)"$/
     */
    public function thisShippingMethodNameShouldBe(ShippingMethodInterface $shippingMethod, $shippingMethodName)
    {
        $this->iWantToBrowseShippingMethods();

        Assert::true($this->indexPage->isSingleResourceOnPage([
            'code' => $shippingMethod->getCode(),
            'name' => $shippingMethodName,
        ]));
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter shipping method %s.', $element));
    }

    /**
     * @Then I should be notified that code needs to contain only specific symbols
     */
    public function iShouldBeNotifiedThatCodeShouldContain()
    {
        $this->assertFieldValidationMessage(
            'code',
            'Shipping method code can only be comprised of letters, numbers, dashes and underscores.'
        );
    }

    /**
     * @When I archive the :name shipping method
     */
    public function iArchiveTheShippingMethod($name)
    {
        $actions = $this->indexPage->getActionsForResource(['name' => $name]);
        $actions->pressButton('Archive');
    }

    /**
     * @When I restore the :name shipping method
     */
    public function iRestoreTheShippingMethod($name)
    {
        $actions = $this->indexPage->getActionsForResource(['name' => $name]);
        $actions->pressButton('Restore');
    }

    /**
     * @Then I should be viewing non archival shipping methods
     */
    public function iShouldBeViewingNonArchivalShippingMethods()
    {
        Assert::false($this->indexPage->isArchivalFilterEnabled());
    }

    /**
     * @Then I should see :count shipping methods on the list
     */
    public function thereShouldBeNoShippingMethodsOnTheList($count)
    {
        Assert::same($this->indexPage->countItems(), (int) $count);
    }

    /**
     * @Then the only shipping method on the list should be :name
     */
    public function theOnlyShippingMethodOnTheListShouldBe($name)
    {
        Assert::same((int) $this->indexPage->countItems(), 1);
        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $name]));
    }

    /**
     * @Then shipping method with :element :name should not be added
     */
    public function shippingMethodWithElementValueShouldNotBeAdded($element, $name)
    {
        $this->iWantToBrowseShippingMethods();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $name]));
    }

    /**
     * @When I do not name it
     */
    public function iDoNotNameIt()
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When I do not specify its zone
     */
    public function iDoNotSpecifyItsZone()
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When I remove its zone
     */
    public function iRemoveItsZone()
    {
        $this->updatePage->removeZone();
    }

    /**
     * @Then I should be notified that :element has to be selected
     */
    public function iShouldBeNotifiedThatElementHasToBeSelected($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('Please select shipping method %s.', $element));
    }

    /**
     * @When I remove its name from :language translation
     */
    public function iRemoveItsNameFromTranslation($language)
    {
        $this->createPage->nameIt(null, $language);
    }

    /**
     * @Given I am browsing shipping methods
     * @When I want to browse shipping methods
     */
    public function iWantToBrowseShippingMethods()
    {
        $this->indexPage->open();
    }

    /**
     * @Given I am browsing archival shipping methods
     */
    public function iAmBrowsingArchivalShippingMethods()
    {
        $this->indexPage->open();
        $this->indexPage->chooseArchival('Yes');
        $this->indexPage->filter();
    }

    /**
     * @Given I filter archival shipping methods
     */
    public function iFilterArchivalShippingMethods()
    {
        $this->indexPage->chooseArchival('Yes');
        $this->indexPage->filter();
    }

    /**
     * @Then the first shipping method on the list should have :field :value
     */
    public function theFirstShippingMethodOnTheListShouldHave($field, $value)
    {
        $fields = $this->indexPage->getColumnFields($field);

        Assert::same(reset($fields), $value);
    }

    /**
     * @Then the last shipping method on the list should have :field :value
     */
    public function theLastShippingMethodOnTheListShouldHave($field, $value)
    {
        $fields = $this->indexPage->getColumnFields($field);

        Assert::same(end($fields), $value);
    }

    /**
     * @When I switch the way shipping methods are sorted by :field
     * @When I start sorting shipping methods by :field
     * @Given the shipping methods are already sorted by :field
     */
    public function iSortShippingMethodsBy($field)
    {
        $this->indexPage->sortBy($field);
    }

    /**
     * @Then I should see :numberOfShippingMethods shipping methods in the list
     */
    public function iShouldSeeShippingMethodsInTheList($numberOfShippingMethods)
    {
        Assert::same($this->indexPage->countItems(), (int) $numberOfShippingMethods);
    }

    /**
     * @When I enable it
     */
    public function iEnableIt()
    {
        $this->updatePage->enable();
    }

    /**
     * @When I disable it
     */
    public function iDisableIt()
    {
        $this->updatePage->disable();
    }

    /**
     * @Then /^(this shipping method) should be disabled$/
     */
    public function thisShippingMethodShouldBeDisabled(ShippingMethodInterface $shippingMethod)
    {
        $this->assertShippingMethodState($shippingMethod, false);
    }

    /**
     * @Then /^(this shipping method) should be enabled$/
     */
    public function thisShippingMethodShouldBeEnabled(ShippingMethodInterface $shippingMethod)
    {
        $this->assertShippingMethodState($shippingMethod, true);
    }

    /**
     * @When I delete shipping method :shippingMethod
     * @When I try to delete shipping method :shippingMethod
     */
    public function iDeleteShippingMethod(ShippingMethodInterface $shippingMethod)
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['name' => $shippingMethod->getName()]);
    }

    /**
     * @Then /^(this shipping method) should no longer exist in the registry$/
     */
    public function thisShippingMethodShouldNoLongerExistInTheRegistry(ShippingMethodInterface $shippingMethod)
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $shippingMethod->getCode()]));
    }

    /**
     * @Then I should be notified that it is in use
     */
    public function iShouldBeNotifiedThatItIsInUse()
    {
        $this->notificationChecker->checkNotification('Cannot delete, the shipping method is in use.', NotificationType::failure());
    }

    /**
     * @Then I should be notified that amount for :channel channel should not be blank
     */
    public function iShouldBeNotifiedThatAmountForChannelShouldNotBeBlank(ChannelInterface $channel)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same(
            $currentPage->getValidationMessageForAmount($channel->getCode()),
            'This value should not be blank.'
        );
    }

    /**
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage($element, $expectedMessage)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage($element), $expectedMessage);
    }

    /**
     * @param ShippingMethodInterface $shippingMethod
     * @param bool $state
     */
    private function assertShippingMethodState(ShippingMethodInterface $shippingMethod, $state)
    {
        $this->iWantToBrowseShippingMethods();

        Assert::true($this->indexPage->isSingleResourceOnPage([
            'name' => $shippingMethod->getName(),
            'enabled' => $state,
        ]));
    }
}
