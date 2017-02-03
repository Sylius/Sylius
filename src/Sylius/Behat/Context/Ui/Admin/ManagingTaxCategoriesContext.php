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
use Sylius\Behat\Page\Admin\TaxCategory\CreatePageInterface;
use Sylius\Behat\Page\Admin\TaxCategory\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ManagingTaxCategoriesContext implements Context
{
    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     */
    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver
    ) {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
    }

    /**
     * @When I delete tax category :taxCategory
     */
    public function iDeletedTaxCategory(TaxCategoryInterface $taxCategory)
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['code' => $taxCategory->getCode()]);
    }

    /**
     * @Then /^(this tax category) should no longer exist in the registry$/
     */
    public function thisTaxCategoryShouldNoLongerExistInTheRegistry(TaxCategoryInterface $taxCategory)
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $taxCategory->getCode()]));
    }

    /**
     * @Given I want to create a new tax category
     */
    public function iWantToCreateNewTaxCategory()
    {
        $this->createPage->open();
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
     * @When I name it :name
     * @When I rename it to :name
     * @When I do not name it
     * @When I remove its name
     */
    public function iNameIt($name = null)
    {
        $this->createPage->nameIt($name);
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
     * @Then the tax category :taxCategoryName should appear in the registry
     */
    public function theTaxCategoryShouldAppearInTheRegistry($taxCategoryName)
    {
        $this->indexPage->open();
        Assert::true($this->indexPage->isSingleResourceOnPage(['nameAndDescription' => $taxCategoryName]));
    }

    /**
     * @When I describe it as :description
     */
    public function iDescribeItAs($description)
    {
        $this->createPage->describeItAs($description);
    }

    /**
     * @Given I want to modify a tax category :taxCategory
     * @Given /^I want to modify (this tax category)$/
     */
    public function iWantToModifyTaxCategory(TaxCategoryInterface $taxCategory)
    {
        $this->updatePage->open(['id' => $taxCategory->getId()]);
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
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @Then /^(this tax category) name should be "([^"]+)"$/
     * @Then /^(this tax category) should still be named "([^"]+)"$/
     */
    public function thisTaxCategoryNameShouldBe(TaxCategoryInterface $taxCategory, $taxCategoryName)
    {
        $this->indexPage->open();
        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $taxCategory->getCode(), 'nameAndDescription' => $taxCategoryName]));
    }

    /**
     * @Then I should be notified that tax category with this code already exists
     */
    public function iShouldBeNotifiedThatTaxCategoryWithThisCodeAlreadyExists()
    {
        Assert::same($this->createPage->getValidationMessage('code'), 'The tax category with given code already exists.');
    }

    /**
     * @Then there should still be only one tax category with :element :code
     */
    public function thereShouldStillBeOnlyOneTaxCategoryWith($element, $code)
    {
        $this->indexPage->open();
        Assert::true($this->indexPage->isSingleResourceOnPage([$element => $code]));
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage($element), sprintf('Please enter tax category %s.', $element));
    }

    /**
     * @Then tax category with :element :name should not be added
     */
    public function taxCategoryWithElementValueShouldNotBeAdded($element, $name)
    {
        $this->indexPage->open();
        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $name]));
    }
}
