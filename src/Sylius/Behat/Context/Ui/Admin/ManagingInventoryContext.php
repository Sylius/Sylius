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
use Sylius\Behat\Page\Admin\Inventory\IndexPageInterface;
use Webmozart\Assert\Assert;

final class ManagingInventoryContext implements Context
{
    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @param IndexPageInterface $indexPage
     */
    public function __construct(IndexPageInterface $indexPage)
    {
        $this->indexPage = $indexPage;
    }

    /**
     * @When I want to browse inventory
     */
    public function iWantToBrowseInventory()
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
     * @Then I should see only one tracked variant in the list
     */
    public function iShouldSeeOnlyOneTrackedVariantInTheList()
    {
        Assert::same($this->indexPage->countItems(), 1);
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
}
