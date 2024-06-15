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
use Sylius\Behat\Element\Admin\ProductAttribute\FormElementInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\ProductAttribute\CreatePageInterface;
use Sylius\Behat\Page\Admin\ProductAttribute\UpdatePageInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Webmozart\Assert\Assert;

final readonly class ManagingProductAttributesContext implements Context
{
    public function __construct(
        private CreatePageInterface $createPage,
        private IndexPageInterface $indexPage,
        private UpdatePageInterface $updatePage,
        private FormElementInterface $formElement,
    ) {
    }

    /**
     * @When I want to create a new :type product attribute
     */
    public function iWantToCreateANewTextProductAttribute(string $type): void
    {
        $this->createPage->open(['type' => $type]);
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(string $code = null): void
    {
        $this->formElement->specifyCode($code ?? '');
    }

    /**
     * @When I name it :name in :localeCode
     */
    public function iSpecifyItsNameAs(string $name, string $localeCode): void
    {
        $this->formElement->nameIt($name, $localeCode);
    }

    /**
     * @When I disable its translatability
     */
    public function iDisableItsTranslatability(): void
    {
        $this->formElement->disableTranslatability();
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
     * @When I( also) add value :value in :localeCode
     */
    public function iAddValue(string $value, string $localeCode): void
    {
        $this->formElement->addAttributeValue($value, $localeCode);
    }

    /**
     * @When I delete value :value
     */
    public function iDeleteValue(string $value, string $localeCode = 'en_US'): void
    {
        $this->formElement->deleteAttributeValue($value, $localeCode);
    }

    /**
     * @When I change its value :oldValue to :newValue
     */
    public function iChangeItsValueTo(string $oldValue, string $newValue): void
    {
        $this->formElement->changeAttributeValue($oldValue, $newValue, 'en_US');
    }

    /**
     * @Then I should see the product attribute :name in the list
     */
    public function iShouldSeeTheProductAttributeInTheList(string $name): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $name]));
    }

    /**
     * @Then the :type attribute :name should appear in the store
     * @Then the :type attribute :name should still be in the store
     */
    public function theAttributeShouldAppearInTheStore(string $type, string $name): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceWithSpecificElementOnPage(
            ['name' => $name],
            sprintf('td span.ui.label:contains("%s")', ucfirst($type)),
        ));
    }

    /**
     * @When /^I want to edit (this product attribute)$/
     */
    public function iWantToEditThisAttribute(ProductAttributeInterface $productAttribute): void
    {
        $this->updatePage->open(['id' => $productAttribute->getId()]);
    }

    /**
     * @When I change its name to :name in :localeCode
     */
    public function iChangeItNameToIn(string $name, string $localeCode): void
    {
        $this->formElement->changeName($name, $localeCode);
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
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        Assert::true($this->formElement->isCodeDisabled());
    }

    /**
     * @Then the type field should be disabled
     * @Then I should not be able to edit its type
     */
    public function theTypeFieldShouldBeDisabled(): void
    {
        Assert::true($this->formElement->isTypeDisabled());
    }

    /**
     * @Then I should be notified that product attribute with this code already exists
     */
    public function iShouldBeNotifiedThatProductAttributeWithThisCodeAlreadyExists(): void
    {
        Assert::same($this->formElement->getValidationMessage('code'), 'This code is already in use.');
    }

    /**
     * @Then there should still be only one product attribute with code :code
     */
    public function thereShouldStillBeOnlyOneProductAttributeWithCode(string $code): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $code]));
    }

    /**
     * @When I do not name it
     */
    public function iDoNotNameIt(): void
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired(string $element): void
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter attribute %s.', $element));
    }

    /**
     * @Given the attribute with :elementName :elementValue should not appear in the store
     */
    public function theAttributeWithCodeShouldNotAppearInTheStore(string $elementName, string $elementValue): void
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage([$elementName => $elementValue]));
    }

    /**
     * @When I remove its name from :localeCode translation
     */
    public function iRemoveItsNameFromTranslation(string $localeCode): void
    {
        $this->formElement->changeName('', $localeCode);
    }

    /**
     * @When I browse product attributes
     * @When I want to see all product attributes in store
     */
    public function iWantToSeeAllProductAttributesInStore(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When I specify its min length as :min
     * @When I specify its min entries value as :min
     */
    public function iSpecifyItsMinValueAs(int $min): void
    {
        $this->formElement->specifyMinValue($min);
    }

    /**
     * @When I specify its max length as :max
     * @When I specify its max entries value as :max
     */
    public function iSpecifyItsMaxLengthAs(int $max): void
    {
        $this->formElement->specifyMaxValue($max);
    }

    /**
     * @When I check multiple option
     */
    public function iCheckMultipleOption(): void
    {
        $this->formElement->checkMultiple();
    }

    /**
     * @When I do not check multiple option
     */
    public function iDoNotCheckMultipleOption(): void
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When I check (also) the :productAttributeName product attribute
     */
    public function iCheckTheProductAttribute(string $productAttributeName): void
    {
        $this->indexPage->checkResourceOnPage(['name' => $productAttributeName]);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @Then I should see a single product attribute in the list
     * @Then I should see :amountOfProductAttributes product attributes in the list
     */
    public function iShouldSeeCustomersInTheList(int $amountOfProductAttributes = 1): void
    {
        Assert::same($this->indexPage->countItems(), $amountOfProductAttributes);
    }

    /**
     * @When /^I(?:| try to) delete (this product attribute)$/
     */
    public function iDeleteThisProductAttribute(ProductAttributeInterface $productAttribute): void
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['code' => $productAttribute->getCode(), 'name' => $productAttribute->getName()]);
    }

    /**
     * @Then /^(this product attribute) should no longer exist in the registry$/
     */
    public function thisProductAttributeShouldNoLongerExistInTheRegistry(ProductAttributeInterface $productAttribute): void
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $productAttribute->getCode()]));
    }

    /**
     * @Then the first product attribute on the list should have name :name
     */
    public function theFirstProductAttributeOnTheListShouldHave(string $name): void
    {
        $names = $this->indexPage->getColumnFields('name');

        Assert::same(reset($names), $name);
    }

    /**
     * @Then the last product attribute on the list should have name :name
     */
    public function theLastProductAttributeOnTheListShouldHave(string $name): void
    {
        $names = $this->indexPage->getColumnFields('name');

        Assert::same(end($names), $name);
    }

    /**
     * @Then I should see the value :value in :localeCode locale
     */
    public function iShouldSeeTheValue(string $value, string $localeCode): void
    {
        Assert::true($this->formElement->hasAttributeValue($value, $localeCode));
    }

    /**
     * @Then I should not see the value :value in :localeCode locale
     */
    public function iShouldNotSeeTheValue(string $value, string $localeCode): void
    {
        Assert::false($this->formElement->hasAttributeValue($value, $localeCode));
    }

    /**
     * @Then /^(this product attribute) should have value "([^"]*)"/
     */
    public function theSelectAttributeShouldHaveValue(ProductAttributeInterface $productAttribute, string $value): void
    {
        $this->iWantToEditThisAttribute($productAttribute);

        Assert::true($this->formElement->hasAttributeValue($value, 'en_US'));
    }

    /**
     * @Then I should be notified that max length must be greater or equal to the min length
     */
    public function iShouldBeNotifiedThatMaxLengthMustBeGreaterOrEqualToTheMinLength(): void
    {
        Assert::same(
            $this->formElement->getValidationErrors(),
            'Configuration max length must be greater or equal to the min length.',
        );
    }

    /**
     * @Then I should be notified that max entries value must be greater or equal to the min entries value
     */
    public function iShouldBeNotifiedThatMaxEntriesValueMustBeGreaterOrEqualToTheMinEntriesValue(): void
    {
        Assert::same(
            $this->formElement->getValidationErrors(),
            'Configuration max entries value must be greater or equal to the min entries value.',
        );
    }

    /**
     * @Then I should be notified that min entries value must be lower or equal to the number of added choices
     */
    public function iShouldBeNotifiedThatMinEntriesValueMustBeLowerOrEqualToTheNumberOfAddedChoices(): void
    {
        Assert::same(
            $this->formElement->getValidationErrors(),
            'Configuration min entries value must be lower or equal to the number of added choices.',
        );
    }

    /**
     * @Then I should be notified that multiple must be true if min or max entries values are specified
     */
    public function iShouldBeNotifiedThatMultipleMustBeTrueIfMinOrMaxEntriesValuesAreSpecified(): void
    {
        Assert::same(
            $this->formElement->getValidationErrors(),
            'Configuration multiple must be true if min or max entries values are specified.',
        );
    }

    /**
     * @Then /^(this product attribute) should not have value "([^"]*)"/
     */
    public function theSelectAttributeShouldNotHaveValue(ProductAttributeInterface $productAttribute, string $value): void
    {
        $this->iWantToEditThisAttribute($productAttribute);

        Assert::false($this->formElement->hasAttributeValue($value, 'en_US'));
    }
    private function assertFieldValidationMessage(string $element, string $expectedMessage): void
    {
        Assert::same($this->formElement->getValidationMessage($element), $expectedMessage);
    }
}
