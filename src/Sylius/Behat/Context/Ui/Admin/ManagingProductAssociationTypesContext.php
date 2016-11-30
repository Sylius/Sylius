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
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
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
    public function iWantToModifyAPaymentMethod(ProductAssociationTypeInterface $productAssociationType)
    {
        $this->updatePage->open(['id' => $productAssociationType->getId()]);
    }

    /**
     * @When I specify its name as :name
     * @When I do not name it
     */
    public function iSpecifyItsNameAs($name = null)
    {
        $this->createPage->nameIt($name);
    }

    /**
     * @When I rename it to :name
     * @When I remove its name
     */
    public function iRenameItTo($name = null)
    {
        $this->updatePage->nameIt($name);
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null)
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I delete the :productAssociationType product association type
     */
    public function iDeleteTheProductAssociationType(ProductAssociationTypeInterface $productAssociationType)
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
    public function theProductAssociationTypeShouldAppearInTheStore(ProductAssociationTypeInterface $productAssociationType)
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
     * @Then /^(this product association type) should still be named "([^"]+)"$/
     */
    public function thisProductAssociationTypeNameShouldBe(
        ProductAssociationTypeInterface $productAssociationType,
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
        ProductAssociationTypeInterface $productAssociationType
    ) {
        Assert::false(
            $this->indexPage->isSingleResourceOnPage([
                'code' => $productAssociationType->getCode(),
                'name' => $productAssociationType->getName()]
            ),
            sprintf(
                'Product association type %s should no longer exist in the registry',
                $productAssociationType->getName()
            )
        );
    }

    /**
     * @Then I should be notified that product association type with this code already exists
     */
    public function iShouldBeNotifiedThatProductAssociationTypeWithThisCodeAlreadyExists()
    {
        Assert::same(
            $this->createPage->getValidationMessage('code'),
            'The association type with given code already exists.'
        );
    }

    /**
     * @Then there should still be only one product association type with a :element :code
     */
    public function thereShouldStillBeOnlyOneProductAssociationTypeWith($element, $code)
    {
        $this->iWantToBrowseProductAssociationTypes();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage([$element => $code]),
            sprintf('Association type with %s %s cannot be found.', $element, $code)
        );
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $this->assertFieldValidationMessage(
            $element,
            sprintf('Please enter association type %s.', $element)
        );
    }

    /**
     * @Then the product association type with :element :value should not be added
     */
    public function theProductAssociationTypeWithElementValueShouldNotBeAdded($element, $value)
    {
        $this->iWantToBrowseProductAssociationTypes();

        Assert::false(
            $this->indexPage->isSingleResourceOnPage([$element => $value]),
            sprintf('Product association type with %s %s was created, but it should not.', $element, $value)
        );
    }

    /**
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage($element, $expectedMessage)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage($element), $expectedMessage);
    }
}
