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
use Sylius\Behat\Page\Admin\TaxCategory\UpdatePageInterface;
use Sylius\Behat\Page\Admin\TaxCategory\CreatePageInterface;
use Sylius\Behat\Service\CurrentPageResolverInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ManagingTaxCategoriesContext implements Context
{
    const RESOURCE_NAME = 'tax_category';

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
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
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
        Assert::false(
            $this->indexPage->isResourceOnPage(['code' => $taxCategory->getCode()]),
            sprintf('Tax category with code %s exists but should not.', $taxCategory->getCode())
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedAboutSuccessfulDeletion()
    {
        $this->notificationChecker->checkDeletionNotification(self::RESOURCE_NAME);
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
        Assert::true(
            $this->indexPage->isResourceOnPage(['name' => $taxCategoryName]),
            sprintf('Tax category with name %s has not been found.', $taxCategoryName)
        );
    }

    /**
     * @When I describe it as :description
     */
    public function iDescribeItAs($description)
    {
        $this->createPage->describeItAs($description);
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedItHasBeenSuccessfulCreation()
    {
        $this->notificationChecker->checkCreationNotification(self::RESOURCE_NAME);
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
        Assert::true(
            $this->updatePage->isCodeDisabled(),
            'Code should be immutable, but it does not.'
        );
    }

    /**
     * @Then I should be notified about successful edition
     */
    public function iShouldBeNotifiedAboutSuccessfulEdition()
    {
        $this->notificationChecker->checkEditionNotification(self::RESOURCE_NAME);
    }

    /**
     * @Then /^(this tax category) name should be "([^"]+)"$/
     * @Then /^(this tax category) should still be named "([^"]+)"$/
     */
    public function thisTaxCategoryNameShouldBe(TaxCategoryInterface $taxCategory, $taxCategoryName)
    {
        $this->indexPage->open();
        Assert::true(
            $this->indexPage->isResourceOnPage(['code' => $taxCategory->getCode(), 'name' => $taxCategoryName]),
            sprintf('Tax category name %s was not assigned properly.', $taxCategoryName)
        );
    }

    /**
     * @Then I should be notified that tax category with this code already exists
     */
    public function iShouldBeNotifiedThatTaxCategoryWithThisCodeAlreadyExists()
    {
        Assert::true(
            $this->createPage->checkValidationMessageFor('code', 'The tax category with given code already exists.'),
            'Unique code violation message should appear on page, but it does not.'
        );
    }

    /**
     * @Then there should still be only one tax category with :element :code
     */
    public function thereShouldStillBeOnlyOneTaxCategoryWith($element, $code)
    {
        $this->indexPage->open();
        Assert::true(
            $this->indexPage->isResourceOnPage([$element => $code]),
            sprintf('Tax category with %s %s cannot be founded.', $element, $code)
        );
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm($this->createPage, $this->updatePage);

        Assert::true(
            $currentPage->checkValidationMessageFor($element, sprintf('Please enter tax category %s.', $element)),
            sprintf('Tax category %s should be required.', $element)
        );
    }

    /**
     * @Then tax category with :element :name should not be added
     */
    public function taxCategoryWithElementValueShouldNotBeAdded($element, $name)
    {
        $this->indexPage->open();
        Assert::false(
            $this->indexPage->isResourceOnPage([$element => $name]),
            sprintf('Tax category with %s %s was created, but it should not.', $element, $name)
        );
    }
}
