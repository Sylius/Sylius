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
use Sylius\Behat\Page\Admin\Inventory\IndexPageInterface;
use Webmozart\Assert\Assert;

final class ManagingInventoryContext implements Context
{
    public function __construct(private IndexPageInterface $indexPage)
    {
    }

    /**
     * @Given I am browsing inventory
     * @When I want to browse inventory
     */
    public function iWantToBrowseInventory(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When /^I filter tracked variants with (code|name) containing "([^"]+)"/
     */
    public function iFilterTrackedVariantsWithCodeContaining($field, $value)
    {
        $this->indexPage->specifyFilterType($field, 'Contains');
        $this->indexPage->specifyFilterValue($field, $value);

        $this->indexPage->filter();
    }

    /**
     * @When I filter tracked variants by :productName product
     */
    public function iFilterTrackedVariantsByProduct(string $productName): void
    {
        $this->indexPage->filterByProduct($productName);
        $this->indexPage->filter();
    }

    /**
     * @When I sort the tracked variants :sortingOrder by :field
     */
    public function iSortTrackedVariantsBy(string $sortingOrder, string $field): void
    {
        $this->indexPage->sortBy($field, $sortingOrder === 'descending' ? 'desc' : 'asc');
    }

    /**
     * @Then I should see only one tracked variant in the list
     * @Then I should see :count tracked variants in the list
     */
    public function iShouldSeeTrackedVariantsInTheList(int $count = 1): void
    {
        Assert::same($this->indexPage->countItems(), $count);
    }

    /**
     * @Then I should see that the :productVariantName variant has :quantity quantity on hand
     */
    public function iShouldSeeThatTheProductVariantHasQuantityOnHand($productVariantName, $quantity)
    {
        Assert::true($this->indexPage->isSingleResourceOnPage([
            'name' => $productVariantName,
            'inventory' => sprintf('%s Available on hand', $quantity),
        ]));
    }

    /**
     * @Then the first variant on the list should have :field :name
     */
    public function theFirstVariantOnTheListShouldHave(string $field, string $variantName): void
    {
        $names = $this->indexPage->getColumnFields($field);

        Assert::contains(reset($names), $variantName);
    }

    /**
     * @Then the last variant on the list should have :field :name
     */
    public function theLastVariantOnTheListShouldHave(string $field, string $variantName): void
    {
        $names = $this->indexPage->getColumnFields($field);

        Assert::contains(end($names), $variantName);
    }
}
