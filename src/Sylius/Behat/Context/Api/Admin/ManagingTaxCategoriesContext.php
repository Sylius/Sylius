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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Webmozart\Assert\Assert;

final class ManagingTaxCategoriesContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    public function __construct(ApiClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @When I want to create a new tax category
     */
    public function iWantToCreateNewTaxCategory(): void
    {
        $this->client->buildCreateRequest('tax_categories');
    }

    /**
     * @When I want to modify a tax category :taxCategory
     * @When /^I want to modify (this tax category)$/
     */
    public function iWantToModifyTaxCategory(TaxCategoryInterface $taxCategory): void
    {
        $this->client->buildUpdateRequest('tax_categories', $taxCategory->getCode());
    }

    /**
     * @When I delete tax category :taxCategory
     */
    public function iDeleteTaxCategory(TaxCategoryInterface $taxCategory): void
    {
        $this->client->delete('tax_categories', $taxCategory->getCode());
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(?string $code = null): void
    {
        if ($code !== null) {
            $this->client->addRequestData('code', $code);
        }
    }

    /**
     * @When I name it :name
     * @When I rename it to :name
     * @When I do not name it
     */
    public function iNameIt(?string $name = null): void
    {
        if ($name !== null) {
            $this->client->addRequestData('name', $name);
        }
    }

    /**
     * @When I remove its name
     */
    public function iRemoveItsName(): void
    {
        $this->client->addRequestData('name', '');
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I describe it as :description
     */
    public function iDescribeItAs(string $description): void
    {
        $this->client->addRequestData('description', $description);
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->client->update();
    }

    /**
     * @When I browse tax categories
     */
    public function iWantToBrowseTaxCategories(): void
    {
        $this->client->index('tax_categories');
    }

    /**
     * @Then /^(this tax category) should no longer exist in the registry$/
     */
    public function thisTaxCategoryShouldNoLongerExistInTheRegistry(TaxCategoryInterface $taxCategory): void
    {
        $code = $taxCategory->getCode();
        Assert::false(
            $this->isItemOnIndex('code', $code),
            sprintf('Tax category with code %s exist', $code)
        );
    }

    /**
     * @Then I should see the tax category :taxCategoryName in the list
     * @Then the tax category :taxCategoryName should appear in the registry
     */
    public function theTaxCategoryShouldAppearInTheRegistry(string $taxCategoryName): void
    {
        Assert::true(
            $this->isItemOnIndex('name', $taxCategoryName),
            sprintf('Tax category with name %s does not exist', $taxCategoryName)
        );
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        $this->client->addRequestData('code', 'NEW_CODE');
        $this->client->update();

        Assert::false($this->client->hasValue('code', 'NEW_CODE'), 'The code field with value NEW_CODE exist');
    }

    /**
     * @Then /^(this tax category) name should be "([^"]+)"$/
     * @Then /^(this tax category) should still be named "([^"]+)"$/
     */
    public function thisTaxCategoryNameShouldBe(TaxCategoryInterface $taxCategory, $taxCategoryName): void
    {
        $this->client->show('tax_categories', $taxCategory->getCode());
        Assert::true($this->client->hasValue('name', $taxCategoryName), sprintf('Tax category name is not %s', $taxCategoryName));
    }

    /**
     * @Then I should be notified that tax category with this code already exists
     */
    public function iShouldBeNotifiedThatTaxCategoryWithThisCodeAlreadyExists(): void
    {
        Assert::same($this->client->getError(), 'code: The tax category with given code already exists.');
    }

    /**
     * @Then there should still be only one tax category with :element :value
     */
    public function thereShouldStillBeOnlyOneTaxCategoryWith(string $element, string $value): void
    {
        $this->client->index('tax_categories');
        Assert::same(count($this->client->getCollectionItemsWithValue($element, $value)), 1);
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired(string $element): void
    {
        Assert::contains($this->client->getError(), sprintf('%s: Please enter tax category %s.', $element, $element));
    }

    /**
     * @Then tax category with :element :name should not be added
     */
    public function taxCategoryWithNamedElementShouldNotBeAdded(string $element, string $name): void
    {
        Assert::false($this->isItemOnIndex($element, $name), sprintf('Tax category with %s %s does not exist', $element, $name));
    }

    /**
     * @Then I should see a single tax category in the list
     */
    public function iShouldSeeSingleTaxCategoryInTheList(): void
    {
        Assert::same($this->client->countCollectionItems(), 1);
    }

    private function isItemOnIndex(string $property, string $value): bool
    {
        $this->client->index('tax_categories');

        return $this->client->hasItemWithValue($property, $value);
    }
}
