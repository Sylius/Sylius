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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\ShippingMethod\CreatePageInterface;
use Sylius\Behat\Page\Admin\ShippingMethod\IndexPageInterface;
use Sylius\Behat\Page\Admin\ShippingMethod\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Webmozart\Assert\Assert;

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
    public function iWantToCreateANewShippingMethod(): void
    {
        $this->createPage->open();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null): void
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I specify its position as :position
     */
    public function iSpecifyItsPositionAs($position = null): void
    {
        $this->createPage->specifyPosition($position);
    }

    /**
     * @When I name it :name in :language
     * @When I rename it to :name in :language
     */
    public function iNameItIn($name, $language): void
    {
        $this->createPage->nameIt($name, $language);
    }

    /**
     * @When I describe it as :description in :language
     */
    public function iDescribeItAsIn($description, $language): void
    {
        $this->createPage->describeIt($description, $language);
    }

    /**
     * @When I define it for the :zoneName zone
     */
    public function iDefineItForTheZone($zoneName): void
    {
        $this->createPage->chooseZone($zoneName);
    }

    /**
     * @When I specify its amount as :amount for :channel channel
     */
    public function iSpecifyItsAmountForChannel($amount, ChannelInterface $channel): void
    {
        $this->createPage->specifyAmountForChannel($channel->getCode(), $amount);
    }

    /**
     * @When I make it available in channel :channelName
     */
    public function iMakeItAvailableInChannel($channelName): void
    {
        $this->createPage->checkChannel($channelName);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    /**
     * @When I choose :calculatorName calculator
     * @When I do not specify amount for :calculatorName calculator
     */
    public function iChooseCalculator($calculatorName): void
    {
        $this->createPage->chooseCalculator($calculatorName);
    }

    /**
     * @When I check (also) the :shippingMethodName shipping method
     */
    public function iCheckTheShippingMethod(string $shippingMethodName): void
    {
        $this->indexPage->checkResourceOnPage(['name' => $shippingMethodName]);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @Then I should see the shipping method :shipmentMethodName in the list
     * @Then the shipping method :shipmentMethodName should appear in the registry
     * @Then the shipping method :shipmentMethodName should be in the registry
     */
    public function theShipmentMethodShouldAppearInTheRegistry(string $shipmentMethodName): void
    {
        $this->iWantToBrowseShippingMethods();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $shipmentMethodName]));
    }

    /**
     * @Given /^(this shipping method) should still be in the registry$/
     */
    public function thisShippingMethodShouldStillBeInTheRegistry(ShippingMethodInterface $shippingMethod): void
    {
        $this->theShipmentMethodShouldAppearInTheRegistry($shippingMethod->getName());
    }

    /**
     * @Then the shipping method :shippingMethod should be available in channel :channelName
     */
    public function theShippingMethodShouldBeAvailableInChannel(
        ShippingMethodInterface $shippingMethod,
        $channelName
    ): void {
        $this->iWantToModifyAShippingMethod($shippingMethod);

        Assert::true($this->updatePage->isAvailableInChannel($channelName));
    }

    /**
     * @Then I should be notified that shipping method with this code already exists
     */
    public function iShouldBeNotifiedThatShippingMethodWithThisCodeAlreadyExists(): void
    {
        Assert::same($this->createPage->getValidationMessage('code'), 'The shipping method with given code already exists.');
    }

    /**
     * @Then there should still be only one shipping method with :element :code
     */
    public function thereShouldStillBeOnlyOneShippingMethodWith($element, $code): void
    {
        $this->iWantToBrowseShippingMethods();

        Assert::true($this->indexPage->isSingleResourceOnPage([$element => $code]));
    }

    /**
     * @Given I want to modify a shipping method :shippingMethod
     * @Given /^I want to modify (this shipping method)$/
     */
    public function iWantToModifyAShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->updatePage->open(['id' => $shippingMethod->getId()]);
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled(): void
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @Then /^(this shipping method) name should be "([^"]+)"$/
     * @Then /^(this shipping method) should still be named "([^"]+)"$/
     */
    public function thisShippingMethodNameShouldBe(ShippingMethodInterface $shippingMethod, $shippingMethodName): void
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
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element): void
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter shipping method %s.', $element));
    }

    /**
     * @Then I should be notified that code needs to contain only specific symbols
     */
    public function iShouldBeNotifiedThatCodeShouldContain(): void
    {
        $this->assertFieldValidationMessage(
            'code',
            'Shipping method code can only be comprised of letters, numbers, dashes and underscores.'
        );
    }

    /**
     * @When I archive the :name shipping method
     */
    public function iArchiveTheShippingMethod($name): void
    {
        $actions = $this->indexPage->getActionsForResource(['name' => $name]);
        $actions->pressButton('Archive');
    }

    /**
     * @When I restore the :name shipping method
     */
    public function iRestoreTheShippingMethod($name): void
    {
        $actions = $this->indexPage->getActionsForResource(['name' => $name]);
        $actions->pressButton('Restore');
    }

    /**
     * @Then I should be viewing non archival shipping methods
     */
    public function iShouldBeViewingNonArchivalShippingMethods(): void
    {
        Assert::false($this->indexPage->isArchivalFilterEnabled());
    }

    /**
     * @Then I should see a single shipping method in the list
     * @Then I should see :numberOfShippingMethods shipping methods in the list
     * @Then I should see :numberOfShippingMethods shipping methods on the list
     */
    public function thereShouldBeNoShippingMethodsOnTheList(int $numberOfShippingMethods = 1): void
    {
        Assert::same($this->indexPage->countItems(), $numberOfShippingMethods);
    }

    /**
     * @Then the only shipping method on the list should be :name
     */
    public function theOnlyShippingMethodOnTheListShouldBe($name): void
    {
        Assert::same((int) $this->indexPage->countItems(), 1);
        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $name]));
    }

    /**
     * @Then shipping method with :element :name should not be added
     */
    public function shippingMethodWithElementValueShouldNotBeAdded($element, $name): void
    {
        $this->iWantToBrowseShippingMethods();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $name]));
    }

    /**
     * @When I do not name it
     */
    public function iDoNotNameIt(): void
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When I do not specify its zone
     */
    public function iDoNotSpecifyItsZone(): void
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When I remove its zone
     */
    public function iRemoveItsZone(): void
    {
        $this->updatePage->removeZone();
    }

    /**
     * @Then I should be notified that :element has to be selected
     */
    public function iShouldBeNotifiedThatElementHasToBeSelected($element): void
    {
        $this->assertFieldValidationMessage($element, sprintf('Please select shipping method %s.', $element));
    }

    /**
     * @When I remove its name from :language translation
     */
    public function iRemoveItsNameFromTranslation($language): void
    {
        $this->createPage->nameIt(null, $language);
    }

    /**
     * @Given I am browsing shipping methods
     * @When I browse shipping methods
     * @When I want to browse shipping methods
     */
    public function iWantToBrowseShippingMethods(): void
    {
        $this->indexPage->open();
    }

    /**
     * @Given I am browsing archival shipping methods
     */
    public function iAmBrowsingArchivalShippingMethods(): void
    {
        $this->indexPage->open();
        $this->indexPage->chooseArchival('Yes');
        $this->indexPage->filter();
    }

    /**
     * @Given I filter archival shipping methods
     */
    public function iFilterArchivalShippingMethods(): void
    {
        $this->indexPage->chooseArchival('Yes');
        $this->indexPage->filter();
    }

    /**
     * @Then the first shipping method on the list should have :field :value
     */
    public function theFirstShippingMethodOnTheListShouldHave($field, $value): void
    {
        $fields = $this->indexPage->getColumnFields($field);

        Assert::same(reset($fields), $value);
    }

    /**
     * @Then the last shipping method on the list should have :field :value
     */
    public function theLastShippingMethodOnTheListShouldHave($field, $value): void
    {
        $fields = $this->indexPage->getColumnFields($field);

        Assert::same(end($fields), $value);
    }

    /**
     * @When I switch the way shipping methods are sorted by :field
     * @When I start sorting shipping methods by :field
     * @Given the shipping methods are already sorted by :field
     */
    public function iSortShippingMethodsBy($field): void
    {
        $this->indexPage->sortBy($field);
    }

    /**
     * @When I enable it
     */
    public function iEnableIt(): void
    {
        $this->updatePage->enable();
    }

    /**
     * @When I disable it
     */
    public function iDisableIt(): void
    {
        $this->updatePage->disable();
    }

    /**
     * @Then /^(this shipping method) should be disabled$/
     */
    public function thisShippingMethodShouldBeDisabled(ShippingMethodInterface $shippingMethod): void
    {
        $this->assertShippingMethodState($shippingMethod, false);
    }

    /**
     * @Then /^(this shipping method) should be enabled$/
     */
    public function thisShippingMethodShouldBeEnabled(ShippingMethodInterface $shippingMethod): void
    {
        $this->assertShippingMethodState($shippingMethod, true);
    }

    /**
     * @When I delete shipping method :shippingMethod
     * @When I try to delete shipping method :shippingMethod
     */
    public function iDeleteShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['name' => $shippingMethod->getName()]);
    }

    /**
     * @Then /^(this shipping method) should no longer exist in the registry$/
     */
    public function thisShippingMethodShouldNoLongerExistInTheRegistry(ShippingMethodInterface $shippingMethod): void
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $shippingMethod->getCode()]));
    }

    /**
     * @Then I should be notified that it is in use
     */
    public function iShouldBeNotifiedThatItIsInUse(): void
    {
        $this->notificationChecker->checkNotification('Cannot delete, the shipping method is in use.', NotificationType::failure());
    }

    /**
     * @Then I should be notified that amount for :channel channel should not be blank
     */
    public function iShouldBeNotifiedThatAmountForChannelShouldNotBeBlank(ChannelInterface $channel): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same(
            $currentPage->getValidationMessageForAmount($channel->getCode()),
            'This value should not be blank.'
        );
    }

    private function assertFieldValidationMessage(string $element, string $expectedMessage): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage($element), $expectedMessage);
    }

    private function assertShippingMethodState(ShippingMethodInterface $shippingMethod, bool $state): void
    {
        $this->iWantToBrowseShippingMethods();

        Assert::true($this->indexPage->isSingleResourceOnPage([
            'name' => $shippingMethod->getName(),
            'enabled' => $state ? 'Enabled' : 'Disabled',
        ]));
    }
}
