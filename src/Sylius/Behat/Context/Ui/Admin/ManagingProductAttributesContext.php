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
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\ProductAttribute\CreatePageInterface;
use Sylius\Behat\Page\Admin\ProductAttribute\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Webmozart\Assert\Assert;

final class ManagingProductAttributesContext implements Context
{
    public function __construct(
        private CreatePageInterface $createPage,
        private IndexPageInterface $indexPage,
        private UpdatePageInterface $updatePage,
        private CurrentPageResolverInterface $currentPageResolver,
    ) {
    }

    /**
     * @When I want to create a new :type product attribute
     */
    public function iWantToCreateANewTextProductAttribute($type)
    {
        $this->createPage->open(['type' => $type]);
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null)
    {
        $this->createPage->specifyCode($code ?? '');
    }

    /**
     * @When I name it :name in :language
     */
    public function iSpecifyItsNameAs($name, $language)
    {
        $this->createPage->nameIt($name, $language);
    }

    /**
     * @When I disable its translatability
     */
    public function iDisableItsTranslatability(): void
    {
        $this->createPage->disableTranslation();
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
     * @When I( also) add value :value in :localeCode
     */
    public function iAddValue(string $value, string $localeCode): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->addAttributeValue($value, $localeCode);
    }

    /**
     * @When I delete value :value
     */
    public function iDeleteValue(string $value): void
    {
        $this->updatePage->deleteAttributeValue($value);
    }

    /**
     * @When I change its value :oldValue to :newValue
     */
    public function iChangeItsValueTo(string $oldValue, string $newValue): void
    {
        $this->updatePage->changeAttributeValue($oldValue, $newValue);
    }

    /**
     * @Then I should see the product attribute :name in the list
     */
    public function iShouldSeeTheProductAttributeInTheList($name)
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $name]));
    }

    /**
     * @Then the :type attribute :name should appear in the store
     * @Then the :type attribute :name should still be in the store
     */
    public function theAttributeShouldAppearInTheStore($type, $name)
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
    public function iWantToEditThisAttribute(ProductAttributeInterface $productAttribute)
    {
        $this->updatePage->open(['id' => $productAttribute->getId()]);
    }

    /**
     * @When I change its name to :name in :language
     */
    public function iChangeItNameToIn($name, $language)
    {
        $this->updatePage->changeName($name, $language);
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
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @Then the type field should be disabled
     * @Then I should not be able to edit its type
     */
    public function theTypeFieldShouldBeDisabled(): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::true($currentPage->isTypeDisabled());
    }

    /**
     * @Then I should be notified that product attribute with this code already exists
     */
    public function iShouldBeNotifiedThatProductAttributeWithThisCodeAlreadyExists()
    {
        Assert::same($this->updatePage->getValidationMessage('code'), 'This code is already in use.');
    }

    /**
     * @Then there should still be only one product attribute with code :code
     */
    public function thereShouldStillBeOnlyOneProductAttributeWithCode($code)
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $code]));
    }

    /**
     * @When I do not name it
     */
    public function iDoNotNameIt()
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter attribute %s.', $element));
    }

    /**
     * @Given the attribute with :elementName :elementValue should not appear in the store
     */
    public function theAttributeWithCodeShouldNotAppearInTheStore($elementName, $elementValue)
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage([$elementName => $elementValue]));
    }

    /**
     * @When I remove its name from :language translation
     */
    public function iRemoveItsNameFromTranslation($language)
    {
        $this->updatePage->changeName('', $language);
    }

    /**
     * @When I browse product attributes
     * @When I want to see all product attributes in store
     */
    public function iWantToSeeAllProductAttributesInStore()
    {
        $this->indexPage->open();
    }

    /**
     * @When I specify its min length as :min
     * @When I specify its min entries value as :min
     */
    public function iSpecifyItsMinValueAs(int $min): void
    {
        $this->createPage->specifyMinValue($min);
    }

    /**
     * @When I specify its max length as :max
     * @When I specify its max entries value as :max
     */
    public function iSpecifyItsMaxLengthAs(int $max): void
    {
        $this->createPage->specifyMaxValue($max);
    }

    /**
     * @When I check multiple option
     */
    public function iCheckMultipleOption(): void
    {
        $this->createPage->checkMultiple();
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
    public function iDeleteThisProductAttribute(ProductAttributeInterface $productAttribute)
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['code' => $productAttribute->getCode(), 'name' => $productAttribute->getName()]);
    }

    /**
     * @Then /^(this product attribute) should no longer exist in the registry$/
     */
    public function thisProductAttributeShouldNoLongerExistInTheRegistry(ProductAttributeInterface $productAttribute)
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $productAttribute->getCode()]));
    }

    /**
     * @Then the first product attribute on the list should have name :name
     */
    public function theFirstProductAttributeOnTheListShouldHave($name)
    {
        $names = $this->indexPage->getColumnFields('name');

        Assert::same(reset($names), $name);
    }

    /**
     * @Then the last product attribute on the list should have name :name
     */
    public function theLastProductAttributeOnTheListShouldHave($name)
    {
        $names = $this->indexPage->getColumnFields('name');

        Assert::same(end($names), $name);
    }

    /**
     * @Then I should see the value :value
     */
    public function iShouldSeeTheValue(string $value): void
    {
        Assert::true($this->updatePage->hasAttributeValue($value));
    }

    /**
     * @Then /^(this product attribute) should have value "([^"]*)"/
     */
    public function theSelectAttributeShouldHaveValue(ProductAttributeInterface $productAttribute, string $value): void
    {
        $this->iWantToEditThisAttribute($productAttribute);

        Assert::true($this->updatePage->hasAttributeValue($value));
    }

    /**
     * @Then I should be notified that max length must be greater or equal to the min length
     */
    public function iShouldBeNotifiedThatMaxLengthMustBeGreaterOrEqualToTheMinLength(): void
    {
        $this->assertValidationMessage(
            'Configuration max length must be greater or equal to the min length.',
        );
    }

    /**
     * @Then I should be notified that max entries value must be greater or equal to the min entries value
     */
    public function iShouldBeNotifiedThatMaxEntriesValueMustBeGreaterOrEqualToTheMinEntriesValue(): void
    {
        $this->assertValidationMessage(
            'Configuration max entries value must be greater or equal to the min entries value.',
        );
    }

    /**
     * @Then I should be notified that min entries value must be lower or equal to the number of added choices
     */
    public function iShouldBeNotifiedThatMinEntriesValueMustBeLowerOrEqualToTheNumberOfAddedChoices(): void
    {
        $this->assertValidationMessage(
            'Configuration min entries value must be lower or equal to the number of added choices.',
        );
    }

    /**
     * @Then I should be notified that multiple must be true if min or max entries values are specified
     */
    public function iShouldBeNotifiedThatMultipleMustBeTrueIfMinOrMaxEntriesValuesAreSpecified(): void
    {
        $this->assertValidationMessage(
            'Configuration multiple must be true if min or max entries values are specified.',
        );
    }

    /**
     * @Then /^(this product attribute) should not have value "([^"]*)"/
     */
    public function theSelectAttributeShouldNotHaveValue(ProductAttributeInterface $productAttribute, string $value): void
    {
        $this->iWantToEditThisAttribute($productAttribute);

        Assert::false($this->updatePage->hasAttributeValue($value));
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertFieldValidationMessage(string $element, string $expectedMessage): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage($element), $expectedMessage);
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertValidationMessage(string $expectedMessage): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationErrors(), $expectedMessage);
    }
}
