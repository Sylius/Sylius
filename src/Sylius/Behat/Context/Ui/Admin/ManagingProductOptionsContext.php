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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Element\Admin\ProductOption\FormElementInterface;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Webmozart\Assert\Assert;

final class ManagingProductOptionsContext implements Context
{
    public function __construct(
        private IndexPageInterface $indexPage,
        private CreatePageInterface $createPage,
        private UpdatePageInterface $updatePage,
        private FormElementInterface $formElement,
    ) {
    }

    /**
     * @When I want to create a new product option
     */
    public function iWantToCreateANewProductOption()
    {
        $this->createPage->open();
    }

    /**
     * @When I want to modify the :productOption product option
     */
    public function iWantToModifyAProductOption(ProductOptionInterface $productOption)
    {
        $this->updatePage->open(['id' => $productOption->getId()]);
    }

    /**
     * @When I specify a too long :field
     */
    public function iSpecifyATooLong(string $field): void
    {
        $this->formElement->specifyField(ucwords($field), str_repeat('a', 256));
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
        $this->formElement->nameItIn($name, $language);
    }

    /**
     * @When I rename it to :name in :language
     * @When I remove its name from :language translation
     */
    public function iRenameItToInLanguage(string $language, ?string $name = null): void
    {
        $this->formElement->nameItIn($name ?? '', $language);
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
    public function iSpecifyItsCodeAs(?string $code = null): void
    {
        $this->formElement->specifyCode($code ?? '');
    }

    /**
     * @When I add the :value option value identified by :code
     */
    public function iAddTheOptionValueWithCodeAndValue(string $value, string $code): void
    {
        $this->formElement->addOptionValue($code, $value);
    }

    /**
     * @When I delete the :value option value of this product option
     */
    public function iDeleteTheOptionValueWithCodeAndValue(string $value): void
    {
        $this->formElement->removeOptionValue($value);
    }

    /**
     * @When I check (also) the :productOptionName product option
     */
    public function iCheckTheProductOption(string $productOptionName): void
    {
        $this->indexPage->checkResourceOnPage(['name' => $productOptionName]);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @Then I should see the product option :productOptionName in the list
     * @Then the product option :productOptionName should appear in the registry
     * @Then the product option :productOptionName should be in the registry
     */
    public function theProductOptionShouldAppearInTheRegistry(string $productOptionName): void
    {
        $this->iBrowseProductOptions();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $productOptionName]));
    }

    /**
     * @Then I should be notified that product option with this code already exists
     */
    public function iShouldBeNotifiedThatProductOptionWithThisCodeAlreadyExists()
    {
        Assert::same($this->formElement->getValidationMessage('code'), 'The option with given code already exists.');
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
        Assert::same($this->formElement->getValidationMessage($element, ['%localeCode%' => 'en_US']), sprintf('Please enter option %s.', $element));
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
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
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
     * @Then I should see a single product option in the list
     * @Then I should see :amount product options in the list
     */
    public function iShouldSeeProductOptionsInTheList(int $amount = 1): void
    {
        Assert::same($this->indexPage->countItems(), $amount);
    }

    /**
     * @Then /^(this product option) should have the "([^"]*)" option value$/
     * @Then /^(product option "[^"]+") should have the "([^"]*)" option value$/
     */
    public function thisProductOptionShouldHaveTheOptionValue(ProductOptionInterface $productOption, $optionValue)
    {
        $this->iWantToModifyAProductOption($productOption);

        Assert::true($this->formElement->isThereOptionValue($optionValue));
    }

    /**
     * @Then /^(this product option) should not have the "([^"]*)" option value$/
     */
    public function thisProductOptionShouldNotHaveTheOptionValue(ProductOptionInterface $productOption, string $optionValue): void
    {
        $this->iWantToModifyAProductOption($productOption);

        Assert::false($this->formElement->isThereOptionValue($optionValue));
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

    /**
     * @Then I should be notified that :field is too long
     * @Then I should be notified that :field should be no longer than :maxLength characters
     */
    public function iShouldBeNotifiedThatFieldValueIsTooLong(string $field, int $maxLength = 255): void
    {
        $validationMessage = $this->formElement->getValidationMessage(StringInflector::nameToLowercaseCode($field));

        Assert::contains(
            $validationMessage,
            sprintf('must not be longer than %d characters.', $maxLength),
        );
    }
}
