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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Admin\Helper\ValidationTrait;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Webmozart\Assert\Assert;

final class ManagingShippingCategoriesContext implements Context
{
    use ValidationTrait;

    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
    ) {
    }

    /**
     * @When I want to create a new shipping category
     */
    public function iWantToCreateANewShippingCategory(): void
    {
        $this->client->buildCreateRequest(Resources::SHIPPING_CATEGORIES);
    }

    /**
     * @When I want to modify a shipping category :shippingCategory
     */
    public function iWantToModifyAShippingCategory(ShippingCategoryInterface $shippingCategory): void
    {
        $this->client->buildUpdateRequest(Resources::SHIPPING_CATEGORIES, $shippingCategory->getCode());
    }

    /**
     * @When I (try to) add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I delete shipping category :shippingCategory
     */
    public function iDeleteShippingCategory(ShippingCategoryInterface $shippingCategory): void
    {
        $this->client->delete(Resources::SHIPPING_CATEGORIES, $shippingCategory->getCode());
    }

    /**
     * @When I browse shipping categories
     */
    public function iBrowseShippingCategories(): void
    {
        $this->client->index(Resources::SHIPPING_CATEGORIES);
    }

    /**
     * @When I do not specify its code
     * @When I specify its code as :code
     */
    public function iSpecifyItsCodeAs(?string $code = null): void
    {
        if ($code !== null) {
            $this->client->addRequestData('code', $code);
        }
    }

    /**
     * @When I name it :name
     * @When I do not specify its name
     * @When I rename it to :name
     */
    public function iNameIt(?string $name = null): void
    {
        if ($name !== null) {
            $this->client->addRequestData('name', $name);
        }
    }

    /**
     * @When I modify a shipping category :shippingCategory
     */
    public function iModifyAShippingCategory(ShippingCategoryInterface $shippingCategory): void
    {
        $this->client->buildUpdateRequest(Resources::SHIPPING_CATEGORIES, $shippingCategory->getCode());
    }

    /**
     * @When I specify its description as :description
     */
    public function iSpecifyItsDescriptionAs(string $description): void
    {
        $this->client->addRequestData('description', $description);
    }

    /**
     * @Then I should be notified that shipping category with this code already exists
     */
    public function iShouldBeNotifiedThatShippingCategoryWithThisCodeAlreadyExists(): void
    {
        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'code: The shipping category with given code already exists.',
        );
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatElementIsRequired(string $element): void
    {
        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('%s: Please enter shipping category %s.', $element, $element),
        );
    }

    /**
     * @Then I should see a single shipping category in the list
     * @Then I should see :count shipping categories in the list
     */
    public function iShouldSeeShippingCategoriesInTheList(int $count = 1): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->index(Resources::SHIPPING_CATEGORIES)), $count);
    }

    /**
     * @Then the shipping category :shippingMethodName should be in the registry
     * @Then the shipping category :shippingMethodName should appear in the registry
     */
    public function theShippingCategoryShouldAppearInTheRegistry(string $shippingCategoryName): void
    {
        Assert::true(
            $this->isItemOnIndex('name', $shippingCategoryName),
            sprintf('Shipping category with name %s does not exists', $shippingCategoryName),
        );
    }

    /**
     * @Then shipping category with name :name should not be added
     */
    public function shippingCategoryWithNameShouldNotBeAdded(string $name): void
    {
        Assert::false(
            $this->isItemOnIndex('name', $name),
            sprintf('Shipping category with name %s exists', $name),
        );
    }

    /**
     * @Then /^(this shipping category) should no longer exist in the registry$/
     */
    public function thisShippingCategoryShouldNoLongerExistInTheRegistry(
        ShippingCategoryInterface $shippingCategory,
    ): void {
        $shippingCategoryName = $shippingCategory->getName();
        Assert::false(
            $this->isItemOnIndex('name', $shippingCategoryName),
            sprintf('Shipping category with name %s exist', $shippingCategoryName),
        );
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        $this->client->addRequestData('code', 'NEW_CODE');

        Assert::false(
            $this->responseChecker->hasValue($this->client->update(), 'code', 'NEW_CODE'),
            'The shipping category code should not be changed to "NEW_CODE", but it is',
        );
    }

    /**
     * @Then there should still be only one shipping category with code :code
     */
    public function thereShouldStillBeOnlyOneShippingCategoryWith(string $code): void
    {
        Assert::same(
            count($this->responseChecker->getCollectionItemsWithValue($this->client->index(Resources::SHIPPING_CATEGORIES), 'code', $code)),
            1,
        );
    }

    /**
     * @Then this shipping category name should be :name
     */
    public function thisShippingCategoryNameShouldBe(string $name): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->getLastResponse(), 'name', $name),
            sprintf('Shipping category with name %s does not exists', $name),
        );
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Shipping category could not be created',
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Shipping category could not be deleted',
        );
    }

    private function isItemOnIndex(string $property, string $value): bool
    {
        $this->client->index(Resources::SHIPPING_CATEGORIES);

        return $this->responseChecker->hasItemWithValue($this->client->getLastResponse(), $property, $value);
    }
}
