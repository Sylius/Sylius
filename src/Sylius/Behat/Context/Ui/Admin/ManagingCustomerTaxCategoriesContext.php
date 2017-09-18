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
use Sylius\Behat\Page\Admin\CustomerTaxCategory\CreatePageInterface;
use Sylius\Behat\Page\Admin\CustomerTaxCategory\IndexPageInterface;
use Sylius\Behat\Page\Admin\CustomerTaxCategory\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Core\Model\CustomerTaxCategoryInterface;
use Webmozart\Assert\Assert;

final class ManagingCustomerTaxCategoriesContext implements Context
{
    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     */
    public function __construct(
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
    }

    /**
     * @When I want to create a new customer tax category
     */
    public function iWantToCreateANewCustomerTaxCategory(): void
    {
        $this->createPage->open();
    }

    /**
     * @When I want to modify a customer tax category :customerTaxCategory
     * @When /^I want to modify (this customer tax category)$/
     */
    public function iWantToModifyACustomerTaxCategory(CustomerTaxCategoryInterface $customerTaxCategory)
    {
        $this->updatePage->open(['id' => $customerTaxCategory->getId()]);
    }

    /**
     * @Given I am browsing customer tax categories
     * @When I browse customer tax categories
     */
    public function iBrowseCustomerTaxCategories(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(?string $code = null): void
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I name it :name
     * @When I do not name it
     */
    public function iNameIt(?string $name = null): void
    {
        $this->createPage->nameIt($name);
    }

    /**
     * @When I rename it to :name
     * @When I remove its name
     */
    public function iRenameItTo(?string $name = null): void
    {
        $this->updatePage->nameIt($name);
    }

    /**
     * @When I describe it as :description
     */
    public function iDescribeItAs(string $description): void
    {
        $this->createPage->describeItAs($description);
    }

    /**
     * @When I change description to :description
     */
    public function iChangeDescriptionTo(string $description): void
    {
        $this->updatePage->describeItAs($description);
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
     * @When I delete a customer tax category :customerTaxCategory
     */
    public function iDeleteACustomerTaxCategory(CustomerTaxCategoryInterface $customerTaxCategory): void
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['code' => $customerTaxCategory->getCode()]);
    }

    /**
     * @When I switch the way customer tax categories are sorted by name and description field
     */
    public function iSwitchTheWayCustomerTaxCategoriesAreSortedByNameAndDescriptionField(): void
    {
        $this->indexPage->sortBy('nameAndDescription');
    }

    /**
     * @Given the customer tax categories are already sorted by code
     * @When I start sorting customer tax categories by code
     * @When I switch the way customer tax categories are sorted by code
     */
    public function iStartSortingCustomerTaxCategoriesByCode(): void
    {
        $this->indexPage->sortBy('code');
    }

    /**
     * @When I filter customer tax categories with value containing :searchValue
     */
    public function iFilterCustomerTaxCategoriesWithValueContaining(string $searchValue): void
    {
        $this->indexPage->specifySearchValue($searchValue);
        $this->indexPage->filter();
    }

    /**
     * @Then the customer tax category :name should appear in the registry
     * @Then I should (also) see the customer tax category :name in the list
     */
    public function theCustomerTaxCategoryShouldAppearInTheRegistry(string $name): void
    {
        $this->indexPage->open();
        Assert::true($this->indexPage->isSingleResourceOnPage(['nameAndDescription' => $name]));
    }

    /**
     * @Then /^(this customer tax category) description should be "([^"]+)"$/
     * @Then the customer tax category :customerTaxCategory description should be :description
     */
    public function thisCustomerTaxCategoryDescriptionShouldBe(
        CustomerTaxCategoryInterface $customerTaxCategory,
        string $description
    ): void {
        $this->iWantToModifyACustomerTaxCategory($customerTaxCategory);

        Assert::same($this->updatePage->getDescription(), $description);
    }

    /**
     * @Then I should see :amount customer tax categories in the list
     * @Then I should see a single customer tax category in the list
     */
    public function iShouldSeeCustomerTaxCategoriesInTheList(int $amount = 1): void
    {
        Assert::same($this->indexPage->countItems(), $amount);
    }

    /**
     * @Then I should be notified that a customer tax category with this code already exists
     */
    public function iShouldBeNotifiedThatACustomerTaxCategoryWithThisCodeAlreadyExists(): void
    {
        Assert::same(
            $this->createPage->getValidationMessage('code'),
            'The customer tax category with given code already exists.'
        );
    }

    /**
     * @Then there should still be only one customer tax category with a code :code
     */
    public function thereShouldStillBeOnlyOneCustomerTaxCategoryWithACode(string $code): void
    {
        $this->iBrowseCustomerTaxCategories();

        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $code]));
    }

    /**
     * @Then /^I should be notified that the (code|name) is required$/
     */
    public function iShouldBeNotifiedThatIsRequired(string $element): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same(
            $currentPage->getValidationMessage($element),
            sprintf('Please enter a customer tax category %s.', $element)
        );
    }

    /**
     * @Then /^the customer tax category with a (code|name) "([^"]+)" should not be added$/
     */
    public function taxCategoryWithElementValueShouldNotBeAdded(string $element, string $value): void
    {
        $this->iBrowseCustomerTaxCategories();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $value]));
    }

    /**
     * @Then /^(this customer tax category) name should be "([^"]+)"$/
     * @Then /^(this customer tax category) should still be named "([^"]+)"$/
     */
    public function thisCustomerTaxCategoryNameShouldBe(
        CustomerTaxCategoryInterface $customerTaxCategory,
        string $name
    ): void {
        $this->iBrowseCustomerTaxCategories();

        Assert::true($this->indexPage->isSingleResourceOnPage([
            'code' => $customerTaxCategory->getCode(),
            'nameAndDescription' => $name
        ]));
    }

    /**
     * @Then /^(this customer tax category) should no longer exist in the registry$/
     */
    public function thisCustomerTaxCategoryShouldNoLongerExistInTheRegistry(
        CustomerTaxCategoryInterface $customerTaxCategory
    ): void {
        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $customerTaxCategory->getCode()]));
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled(): void
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @Then the first customer tax category in the list should be :name
     */
    public function theFirstCustomerTaxCategoryInTheListShouldBe(string $name): void
    {
        $names = $this->indexPage->getColumnFields('nameAndDescription');

        Assert::same(reset($names), $name);
    }

    /**
     * @Then the last customer tax category in the list should be :name
     */
    public function theLastCustomerTaxCategoryInTheListShouldBe(string $name): void
    {
        $names = $this->indexPage->getColumnFields('nameAndDescription');

        Assert::same(end($names), $name);
    }
}
