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
final class ManagingProductAssociationTypesContext implements Context
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
     * @When I want to browse product association types
     */
    public function iWantToBrowseProductAssociationTypes()
    {
        $this->indexPage->open();
    }

    /**
     * @Then I should see :amount product association types in the list
     */
    public function iShouldSeeCustomerGroupsInTheList($amount)
    {
        Assert::same(
            (int) $amount,
            $this->indexPage->countItems(),
            sprintf('Amount of product association types should be equal %s, but is not.', $amount)
        );
    }

    /**
     * @Then I should see the product association type :name in the list
     *
     */
    public function iShouldSeeTheProductAssociationTypeInTheList($name)
    {
        $this->iWantToBrowseProductAssociationTypes();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $name]),
            sprintf('The product association type with a name %s should exist, but it does not.', $name)
        );
    }
}
