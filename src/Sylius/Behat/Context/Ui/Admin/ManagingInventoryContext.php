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
use Sylius\Behat\Page\Admin\Inventory\IndexPageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
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
     * @When I specify filter name as :name
     */
    public function iSpecifyFilterNameAs($name)
    {
        $this->indexPage->specifyFilterValue('name', $name);
    }

    /**
     * @When I specify filter code as :code
     */
    public function iSpecifyFilterCodeAs($code)
    {
        $this->indexPage->specifyFilterValue('code', $code);
    }

    /**
     * @When I choose :type as a filter name type
     */
    public function iChooseTypeAsAFilterNameType($type)
    {
        $this->indexPage->specifyFilterType('name', $type);
    }

    /**
     * @When I choose :type as a filter code type
     */
    public function iChooseTypeAsAFilterCodeType($type)
    {
        $this->indexPage->specifyFilterType('code', $type);
    }

    /**
     * @When I filter
     */
    public function iFilter()
    {
        $this->indexPage->filter();
    }

    /**
     * @Then I should see a single tracked variant(s) in the list
     */
    public function iShouldSeeASingleTrackedVariantsInTheList()
    {
        $foundRows = $this->indexPage->countItems();

        Assert::same(
            1,
            $foundRows,
            '%s rows with tracked product variants should appear on page, %s rows has been found'
        );
    }

    /**
     * @Then I should see that the :productVariantName variant has :quantity quantity on hand
     */
    public function iShouldSeeThatTheProductVariantHasQuantityOnHand($productVariantName, $quantity)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage([
                'name' => $productVariantName,
                'inventory' => sprintf('%s Available on hand', $quantity)
            ]),
            sprintf(
                'This "%s" variant should have %s on hand quantity, but it does not.',
                $productVariantName,
                $quantity
            )
        );
    }
}
