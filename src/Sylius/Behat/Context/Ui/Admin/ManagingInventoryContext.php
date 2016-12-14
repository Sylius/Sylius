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
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
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
     * @Given I want to browse inventory
     */
    public function iWantToBrowseInventory()
    {
        $this->indexPage->open();
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
