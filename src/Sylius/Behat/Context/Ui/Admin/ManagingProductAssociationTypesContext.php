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
use Sylius\Behat\Page\Admin\ProductAssociationType\CreatePageInterface;
use Sylius\Component\Association\Model\AssociationTypeInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ManagingProductAssociationTypesContext implements Context
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
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     */
    public function __construct(CreatePageInterface $createPage, IndexPageInterface $indexPage)
    {
        $this->createPage = $createPage;
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
     * @When I want to create a new product association type
     */
    public function iWantToCreateANewProductAssociationType()
    {
        $this->createPage->open();
    }

    /**
     * @When I specify its name as :name
     */
    public function iSpecifyItsNameAs($name)
    {
        $this->createPage->nameIt($name);
    }

    /**
     * @When I specify its code as :code
     */
    public function iSpecifyItsCodeAs($code)
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
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

    /**
     * @Then the product association type :productAssociationType should appear in the store
     */
    public function theProductAssociationTypeShouldAppearInTheStore(AssociationTypeInterface $productAssociationType)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $productAssociationType->getName()]),
            sprintf(
                'Product association type with name %s should exist but it does not.',
                $productAssociationType->getName()
            )
        );
    }
}
