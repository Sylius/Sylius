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
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ManagingProductOptionsContext implements Context
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
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     */
    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver
    ) {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
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
    public function iWantToModifyAProductOption(ProductOptionInterface $productOption)
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
     */
    public function iNameItInLanguage($name, $language)
    {
        $this->createPage->nameItIn($name, $language);
    }

    /**
     * @When I rename it to :name in :language
     * @When I remove its name from :language translation
     */
    public function iRenameItToInLanguage($name = null, $language)
    {
        $this->updatePage->nameItIn($name, $language);
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
     * @When I add the :value option value identified by :code
     */
    public function iAddTheOptionValueWithCodeAndValue($value, $code)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->addOptionValue($code, $value);
    }

    /**
     * @Then the product option :productOptionName should appear in the registry
     * @Then the product option :productOptionName should be in the registry
     */
    public function theProductOptionShouldAppearInTheRegistry($productOptionName)
    {
        $this->iBrowseProductOptions();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $productOptionName]));
    }

    /**
     * @Then I should be notified that product option with this code already exists
     */
    public function iShouldBeNotifiedThatProductOptionWithThisCodeAlreadyExists()
    {
        Assert::same($this->createPage->getValidationMessage('code'), 'The option with given code already exists.');
    }

    /**
     * @Then there should still be only one product option with :element :value
     */
    public function thereShouldStillBeOnlyOneProductOptionWith($element, $value)
    {
        $this->iBrowseProductOptions();

        Assert::true($this->indexPage->isSingleResourceOnPage([$element => $value]));
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatElementIsRequired($element)
    {
        Assert::same($this->createPage->getValidationMessage($element), sprintf('Please enter option %s.', $element));
    }

    /**
     * @Then the product option with :element :value should not be added
     */
    public function theProductOptionWithElementValueShouldNotBeAdded($element, $value)
    {
        $this->iBrowseProductOptions();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $value]));
    }

    /**
     * @Then /^(this product option) should still be named "([^"]+)"$/
     * @Then /^(this product option) name should be "([^"]+)"$/
     */
    public function thisProductOptionNameShouldStillBe(ProductOptionInterface $productOption, $productOptionName)
    {
        $this->iBrowseProductOptions();

        Assert::true($this->indexPage->isSingleResourceOnPage([
            'code' => $productOption->getCode(),
            'name' => $productOptionName,
        ]));
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true($this->updatePage->isCodeDisabled());
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
        Assert::true($this->createPage->checkValidationMessageForOptionValues('Please add at least 2 option values.'));
    }

    /**
     * @Then /^I should see (\d+) product options in the list$/
     */
    public function iShouldSeeProductOptionsInTheList($amount)
    {
        Assert::same($this->indexPage->countItems(), (int) $amount);
    }

    /**
     * @Then /^(this product option) should have the "([^"]*)" option value$/
     */
    public function thisProductOptionShouldHaveTheOptionValue(ProductOptionInterface $productOption, $optionValue)
    {
        $this->iWantToModifyAProductOption($productOption);

        Assert::true($this->updatePage->isThereOptionValue($optionValue));
    }

    /**
     * @Then the first product option in the list should have :field :value
     */
    public function theFirstProductOptionInTheListShouldHave($field, $value)
    {
        Assert::same($this->indexPage->getColumnFields($field)[0], $value);
    }

    /**
     * @Then the last product option in the list should have :field :value
     */
    public function theLastProductOptionInTheListShouldHave($field, $value)
    {
        $values = $this->indexPage->getColumnFields($field);

        Assert::same(end($values), $value);
    }
}
