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
use Sylius\Behat\Page\Admin\ProductAssociationType\IndexPageInterface;
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
     * @When I name it :name in :language
     */
    public function iNameItIn($name, $language)
    {
        $this->createPage->nameItIn($name, $language);
    }

    /**
     * @When I do not name it
     */
    public function iDoNotNameIt()
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When I rename it to :name in :language
     * @When I remove its name from :language translation
     */
    public function iRenameItToInLanguage($name = null, $language)
    {
        $this->updatePage->nameItIn($name, $language);
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
     * @When /^I filter product association types with (code|name) containing "([^"]+)"/
     */
    public function iFilterProductAssociationTypesWithFieldContaining($field, $value)
    {
        $this->indexPage->specifyFilterType($field, 'Contains');
        $this->indexPage->specifyFilterValue($field, $value);

        $this->indexPage->filter();
    }

    /**
     * @Then I should see :amount product association types in the list
     * @Then I should see only one product association type in the list
     */
    public function iShouldSeeProductAssociationTypesInTheList($amount = 1)
    {
        Assert::same($this->indexPage->countItems(), (int) $amount);
    }

    /**
     * @Then I should see the product association type :name in the list
     *
     */
    public function iShouldSeeTheProductAssociationTypeInTheList($name)
    {
        $this->iWantToBrowseProductAssociationTypes();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $name]));
    }

    /**
     * @Then the product association type :productAssociationType should appear in the store
     */
    public function theProductAssociationTypeShouldAppearInTheStore(ProductAssociationTypeInterface $productAssociationType)
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $productAssociationType->getName()]));
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

        Assert::true($this->indexPage->isSingleResourceOnPage([
            'code' => $productAssociationType->getCode(),
            'name' => $productAssociationTypeName,
        ]));
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @Then /^(this product association type) should no longer exist in the registry$/
     */
    public function thisProductAssociationTypeShouldNoLongerExistInTheRegistry(
        ProductAssociationTypeInterface $productAssociationType
    ) {
        Assert::false($this->indexPage->isSingleResourceOnPage([
            'code' => $productAssociationType->getCode(),
            'name' => $productAssociationType->getName(),
        ]));
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

        Assert::true($this->indexPage->isSingleResourceOnPage([$element => $code]));
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter association type %s.', $element));
    }

    /**
     * @Then the product association type with :element :value should not be added
     */
    public function theProductAssociationTypeWithElementValueShouldNotBeAdded($element, $value)
    {
        $this->iWantToBrowseProductAssociationTypes();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $value]));
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
