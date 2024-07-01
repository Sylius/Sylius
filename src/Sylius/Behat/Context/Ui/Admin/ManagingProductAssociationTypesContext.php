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
use Sylius\Behat\Element\Admin\ProductAssociationType\FormElementInterface;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Behat\Page\Admin\ProductAssociationType\IndexPageInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Webmozart\Assert\Assert;

final readonly class ManagingProductAssociationTypesContext implements Context
{
    public function __construct(
        private CreatePageInterface $createPage,
        private IndexPageInterface $indexPage,
        private UpdatePageInterface $updatePage,
        private FormElementInterface $formElement,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When I browse product association types
     * @When I am browsing product association types
     * @When I want to browse product association types
     */
    public function iWantToBrowseProductAssociationTypes(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When I want to create a new product association type
     */
    public function iWantToCreateANewProductAssociationType(): void
    {
        $this->createPage->open();
    }

    /**
     * @When I want to modify the :productAssociationType product association type
     */
    public function iWantToModifyAPaymentMethod(ProductAssociationTypeInterface $productAssociationType): void
    {
        $this->updatePage->open(['id' => $productAssociationType->getId()]);
    }

    /**
     * @When I name it :name in :language
     */
    public function iNameItIn(string $name, string $language): void
    {
        $this->formElement->setName($name, $language);
    }

    /**
     * @When I do not name it
     */
    public function iDoNotNameIt(): void
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When I rename it to :name in :language
     * @When I remove its name from :language translation
     */
    public function iRenameItToInLanguage(string $language, string $name = ''): void
    {
        $this->formElement->setName($name, $language);
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(string $code = ''): void
    {
        $this->formElement->setCode($code);
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
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I delete the :productAssociationType product association type
     */
    public function iDeleteTheProductAssociationType(ProductAssociationTypeInterface $productAssociationType): void
    {
        $this->iWantToBrowseProductAssociationTypes();

        $this->indexPage->deleteResourceOnPage([
            'code' => $productAssociationType->getCode(),
            'name' => $productAssociationType->getName(),
        ]);
    }

    /**
     * @When I check (also) the :productAssociationTypeName product association type
     */
    public function iCheckTheProductAssociationType(string $productAssociationTypeName): void
    {
        $this->indexPage->checkResourceOnPage(['name' => $productAssociationTypeName]);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @When /^I filter product association types with (code|name) containing "([^"]+)"/
     */
    public function iFilterProductAssociationTypesWithFieldContaining(string $field, string $value): void
    {
        $this->indexPage->specifyFilterType($field, 'Contains');
        $this->indexPage->specifyFilterValue($field, $value);

        $this->indexPage->filter();
    }

    /**
     * @When I sort the product associations :sortType by :field
     */
    public function iSortProductAssociationsBy(string $sortingOrder, string $field): void
    {
        $this->indexPage->sortBy($field, $sortingOrder === 'descending' ? 'desc' : 'asc');
    }

    /**
     * @Then I should see a single product association type in the list
     * @Then I should see only one product association type in the list
     * @Then I should see :amount product association types in the list
     */
    public function iShouldSeeProductAssociationTypesInTheList(int $amount = 1): void
    {
        Assert::same($this->indexPage->countItems(), (int) $amount);
    }

    /**
     * @Then I should see the product association type :name in the list
     */
    public function iShouldSeeTheProductAssociationTypeInTheList(string $name):void
    {
        $this->iWantToBrowseProductAssociationTypes();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $name]));
    }

    /**
     * @Then the product association type :productAssociationType should appear in the store
     */
    public function theProductAssociationTypeShouldAppearInTheStore(ProductAssociationTypeInterface $productAssociationType): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $productAssociationType->getName()]));
    }

    /**
     * @Then /^(this product association type) name should be "([^"]+)"$/
     * @Then /^(this product association type) should still be named "([^"]+)"$/
     */
    public function thisProductAssociationTypeNameShouldBe(
        ProductAssociationTypeInterface $productAssociationType,
        string $productAssociationTypeName,
    ): void {
        $this->iWantToBrowseProductAssociationTypes();

        Assert::true($this->indexPage->isSingleResourceOnPage([
            'code' => $productAssociationType->getCode(),
            'name' => $productAssociationTypeName,
        ]));
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        Assert::true($this->formElement->isCodeDisabled());
    }

    /**
     * @Then /^(this product association type) should no longer exist in the registry$/
     */
    public function thisProductAssociationTypeShouldNoLongerExistInTheRegistry(
        ProductAssociationTypeInterface $productAssociationType,
    ): void {
        Assert::false($this->indexPage->isSingleResourceOnPage([
            'code' => $productAssociationType->getCode(),
            'name' => $productAssociationType->getName(),
        ]));
    }

    /**
     * @Then I should be notified that product association type with this code already exists
     */
    public function iShouldBeNotifiedThatProductAssociationTypeWithThisCodeAlreadyExists(): void
    {
        Assert::same(
            $this->formElement->getValidationMessage('code'),
            'The association type with given code already exists.',
        );
    }

    /**
     * @Then there should still be only one product association type with a :element :code
     */
    public function thereShouldStillBeOnlyOneProductAssociationTypeWith(string $element, string $code): void
    {
        $this->iWantToBrowseProductAssociationTypes();

        Assert::true($this->indexPage->isSingleResourceOnPage([$element => $code]));
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired(string $element): void
    {
        /** @var LocaleInterface $locale */
        $locale = $this->sharedStorage->get('locale');

        Assert::same(
            $this->formElement->getValidationMessage($element, ['%locale%' => $locale->getCode()]),
            sprintf('Please enter association type %s.', $element),
        );
    }

    /**
     * @Then the product association type with :element :value should not be added
     */
    public function theProductAssociationTypeWithElementValueShouldNotBeAdded(string $element, string $value): void
    {
        $this->iWantToBrowseProductAssociationTypes();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $value]));
    }

    /**
     * @Then the first product association on the list should have :field :value
     */
    public function theFirstProductAssociationOnTheListShouldHave(string $field, string $value): void
    {
        $fields = $this->indexPage->getColumnFields($field);

        Assert::same(reset($fields), $value);
    }

    /**
     * @Then the last product association on the list should have :field :value
     */
    public function theLastProductAssociationOnTheListShouldHave(string $field, string $value): void
    {
        $fields = $this->indexPage->getColumnFields($field);

        Assert::same(end($fields), $value);
    }
}
