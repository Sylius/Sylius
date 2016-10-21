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
use Sylius\Behat\Page\Admin\ProductAssociationType\UpdatePageInterface;
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
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     */
    public function __construct(
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
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
     * @When I want to modify the :productAssociationType product association type
     */
    public function iWantToModifyAPaymentMethod(AssociationTypeInterface $productAssociationType)
    {
        $this->updatePage->open(['id' => $productAssociationType->getId()]);
    }

    /**
     * @When I specify its name as :name
     */
    public function iSpecifyItsNameAs($name)
    {
        $this->createPage->nameIt($name);
    }

    /**
     * @When I rename it to :name
     */
    public function iRenameItTo($name)
    {
        $this->updatePage->nameIt($name);
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
     * @When I save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I delete the :productAssociationType product association type
     */
    public function iDeleteTheProductAssociationType(AssociationTypeInterface $productAssociationType)
    {
        $this->iWantToBrowseProductAssociationTypes();

        $this->indexPage->deleteResourceOnPage([
            'code' => $productAssociationType->getCode(),
            'name' => $productAssociationType->getName(),
        ]);
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

    /**
     * @Then /^(this product association type) name should be "([^"]+)"$/
     */
    public function thisProductAssociationTypeNameShouldBe(
        AssociationTypeInterface $productAssociationType,
        $productAssociationTypeName
    ) {
        $this->iWantToBrowseProductAssociationTypes();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(
                [
                    'code' => $productAssociationType->getCode(),
                    'name' => $productAssociationTypeName,
                ]
            ),
            sprintf('Product association type name %s has not been assigned properly.', $productAssociationTypeName)
        );
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true(
            $this->updatePage->isCodeDisabled(),
            'Code field should be disabled'
        );
    }

    /**
     * @Then /^(this product association type) should no longer exist in the registry$/
     */
    public function thisProductAssociationTypeShouldNoLongerExistInTheRegistry(
        AssociationTypeInterface $productAssociationType
    ) {
        Assert::false(
            $this->indexPage->isSingleResourceOnPage([
                'code' => $productAssociationType->getCode(),
                'name' => $productAssociationType->getName()]
            ),
            sprintf(
                'Product association type%s should no longer exist in the registry',
                $productAssociationType->getName()
            )
        );
    }
}
