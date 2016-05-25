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
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\ShippingMethod\CreatePageInterface;
use Sylius\Behat\Page\Admin\ShippingMethod\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\NotificationType;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ManagingShippingMethodsContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

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
     * @param SharedStorageInterface $sharedStorage
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given I want to create a new shipping method
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
     * @When I name it :name in :language
     * @When I rename it to :name in :language
     */
    public function iNameItIn($name, $language)
    {
        $this->createPage->nameIt($name, $language);
    }

    /**
     * @When I define it for the :zoneName zone
     */
    public function iDefineItForTheZone($zoneName)
    {
        $this->createPage->chooseZone($zoneName);
    }

    /**
     * @When I specify its amount as :amount
     */
    public function iSpecifyItsAmountAs($amount)
    {
        $this->createPage->specifyAmount($amount);
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
     * @Then the shipment method :shipmentMethod should appear in the registry
     * @Then the shipment method :shipmentMethod should be in the registry
     */
    public function theShipmentMethodShouldAppearInTheRegistry($shipmentMethodName)
    {
        $this->iWantToBrowseShippingMethods();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $shipmentMethodName]),
            sprintf('The shipping method with name %s has not been found.', $shipmentMethodName)
        );
    }

    /**
     * @Given /^(this shipping method) should still be in the registry$/
     */
    public function thisShippingMethodShouldStillBeInTheRegistry(ShippingMethodInterface $shippingMethod)
    {
        $this->theShipmentMethodShouldAppearInTheRegistry($shippingMethod->getName());
    }

    /**
     * @Then I should be notified that shipping method with this code already exists
     */
    public function iShouldBeNotifiedThatShippingMethodWithThisCodeAlreadyExists()
    {
        Assert::true(
            $this->createPage->checkValidationMessageFor('code', 'The shipping method with given code already exists.'),
            'Unique code violation message should appear on page, but it does not.'
        );
    }

    /**
     * @Then there should still be only one shipping method with :element :code
     */
    public function thereShouldStillBeOnlyOneShippingMethodWith($element, $code)
    {
        $this->iWantToBrowseShippingMethods();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage([$element => $code]),
            sprintf('Shipping method with %s %s cannot be found.', $element, $code)
        );
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
        Assert::true(
            $this->updatePage->isCodeDisabled(),
            'Code should be immutable, but it does not.'
        );
    }

    /**
     * @Then /^(this shipping method) name should be "([^"]+)"$/
     * @Then /^(this shipping method) should still be named "([^"]+)"$/
     */
    public function thisShippingMethodNameShouldBe(ShippingMethodInterface $shippingMethod, $shippingMethodName)
    {
        $this->iWantToBrowseShippingMethods();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(
                [
                    'code' => $shippingMethod->getCode(),
                    'name' => $shippingMethodName,
                ]
            ),
            sprintf('Shipping method name %s has not been assigned properly.', $shippingMethodName)
        );
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
     * @Then shipping method with :element :name should not be added
     */
    public function shippingMethodWithElementValueShouldNotBeAdded($element, $name)
    {
        $this->iWantToBrowseShippingMethods();

        Assert::false(
            $this->indexPage->isSingleResourceOnPage([$element => $name]),
            sprintf('Shipping method with %s %s was created, but it should not.', $element, $name)
        );
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
     * @Then I should be notified that :field should not be blank
     */
    public function iShouldBeNotifiedThatAmountShouldNotBeBlank($field)
    {
        $this->assertFieldValidationMessage($field, 'This value should not be blank.');
    }
    /**
     * @When I want to browse shipping methods
     */
    public function iWantToBrowseShippingMethods()
    {
        $this->indexPage->open();
    }

    /**
     * @Then I should see :numberOfShippingMethods shipping methods in the list
     */
    public function iShouldSeeShippingMethodsInTheList($numberOfShippingMethods)
    {
        $foundRows = $this->indexPage->countItems();

        Assert::true(
            ((int) $numberOfShippingMethods) === $foundRows,
            sprintf('%s rows with shipping methods should appear on page, %s rows has been found', $numberOfShippingMethods, $foundRows)
        );
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
        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['code' => $shippingMethod->getCode()]),
            sprintf('Shipping method with code %s exists but should not.', $shippingMethod->getCode())
        );
    }

    /**
     * @Then I should be notified that it is in use
     */
    public function iShouldBeNotifiedThatItIsInUse()
    {
        $this->notificationChecker->checkNotification('Cannot delete, the shipping method is in use.', NotificationType::failure());
    }

    /**
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage($element, $expectedMessage)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::true(
            $currentPage->checkValidationMessageFor($element, $expectedMessage),
            sprintf('Shipping method %s should be required.', $element)
        );
    }

    /**
     * @param ShippingMethodInterface $shippingMethod
     * @param bool $state
     */
    private function assertShippingMethodState(ShippingMethodInterface $shippingMethod, $state)
    {
        $this->iWantToBrowseShippingMethods();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(
                [
                    'name' => $shippingMethod->getName(),
                    'enabled' => $state,
                ]
            ), sprintf('Shipping method with name %s and state %s has not been found.', $shippingMethod->getName(), $state));
    }
}
