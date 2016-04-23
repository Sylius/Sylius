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
use Sylius\Behat\Page\Admin\ProductOption\CreatePageInterface;
use Sylius\Behat\Page\Admin\ProductOption\UpdatePageInterface;
use Sylius\Behat\Service\CurrentPageResolverInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Product\Model\OptionInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ManagingProductOptionsContext implements Context
{
    const RESOURCE_NAME = 'product_option';

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
     * @Given I want to create a new product option
     */
    public function iWantToCreateANewProductOption()
    {
        $this->createPage->open();
    }

    /**
     * @Given I want to modify the :productOption product option
     */
    public function iWantToModifyAPaymentMethod(OptionInterface $productOption)
    {
        $this->updatePage->open(['id' => $productOption->getId()]);
    }

    /**
     * @When I browse product options
     */
    public function iBrowseProductOptions()
    {
        $this->indexPage->open();
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
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I name it :name in :language
     * @When I remove its name from :language translation
     */
    public function iNameItInLanguage($name = null, $language)
    {
        $this->createPage->nameItIn($name, $language);
    }

    /**
     * @When I do not name it
     */
    public function iDoNotNameIt()
    {
        // Intentionally left blank to fulfill context expectation
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
     * @When I add the option value with code :code and value :value
     */
    public function iAddTheOptionValueWithCodeAndValue($code, $value)
    {
        $this->createPage->addOptionValue($code, $value);
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedItHasBeenSuccessfullyCreated()
    {
        $this->notificationChecker->checkCreationNotification(self::RESOURCE_NAME);
    }

    /**
     * @Then the product option :productOptionName should appear in the registry
     * @Then the product option :productOptionName should be in the registry
     */
    public function theProductOptionShouldAppearInTheRegistry($productOptionName)
    {
        $this->iBrowseProductOptions();

        Assert::true(
            $this->indexPage->isResourceOnPage(['name' => $productOptionName]),
            sprintf('The shipping method with name %s has not been found.', $productOptionName)
        );
    }

    /**
     * @Then I should be notified that product option with this code already exists
     */
    public function iShouldBeNotifiedThatProductOptionWithThisCodeAlreadyExists()
    {
        Assert::true(
            $this->createPage->checkValidationMessageFor('code', 'The option with given code already exists.'),
            'Unique code violation message should appear on page, but it does not.'
        );
    }

    /**
     * @Then there should still be only one product option with :element :value
     */
    public function thereShouldStillBeOnlyOneProductOptionWith($element, $value)
    {
        $this->iBrowseProductOptions();

        Assert::true(
            $this->indexPage->isResourceOnPage([$element => $value]),
            sprintf('Product option with %s %s cannot be found.', $element, $value)
        );
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatElementIsRequired($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter option %s.', $element));
    }

    /**
     * @Then the product option with :element :value should not be added
     */
    public function theProductoptionWithElementValueShouldNotBeAdded($element, $value)
    {
        $this->iBrowseProductOptions();

        Assert::false(
            $this->indexPage->isResourceOnPage([$element => $value]),
            sprintf('Product option with %s %s was created, but it should not.', $element, $value)
        );
    }

    /**
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage($element, $expectedMessage)
    {
        Assert::true(
            $this->createPage->checkValidationMessageFor($element, $expectedMessage),
            sprintf('Product option %s should be required.', $element)
        );
    }

    /**
     * @Then /^(this product option) should still be named "([^"]+)"$/
     */
    public function thisProductOptionNameShouldStillBe(OptionInterface $productOption, $productOptionName)
    {
        $this->iBrowseProductOptions();

        Assert::true(
            $this->indexPage->isResourceOnPage([
                'code' => $productOption->getCode(),
                'name' => $productOptionName,
            ]),
            sprintf('Product option name %s has not been assigned properly.', $productOptionName)
        );
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true(
            $this->updatePage->isCodeDisabled(),
            'Code field should be disabled but it is not'
        );
    }

    /**
     * @When I do not add an option value
     */
    public function iDoNotAddAnOptionValue()
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @Then I should be notified that at least two option values are required
     */
    public function iShouldBeNotifiedThatAtLeastTwoOptionValuesAreRequired()
    {
        Assert::true(
            $this->createPage->checkValidationMessageForOptionValues('Please add at least 2 option values.'),
            'I should be notified that product option needs at least two option values.'
        );
    }

    /**
     * @Then I should see :amount product options in the list
     */
    public function iShouldSeeProductOptionsInTheList($amount)
    {
        $foundRows = $this->indexPage->countItems();

        Assert::eq(
            ((int) $amount),
            $foundRows,
            '%2$s rows with product options should appear on page, %s rows has been found'
        );
    }
}
