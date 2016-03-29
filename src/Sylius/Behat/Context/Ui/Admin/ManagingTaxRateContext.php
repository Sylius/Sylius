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
use Sylius\Behat\Page\Admin\TaxRate\UpdatePageInterface;
use Sylius\Behat\Service\CurrentPageResolverInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Page\Admin\TaxRate\CreatePageInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ManagingTaxRateContext implements Context
{
    const RESOURCE_NAME = 'tax_rate';

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
     * @Given I want to create a new tax rate
     */
    public function iWantToCreateNewTaxRate()
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
     * @When I specify its amount as :amount%
     * @When I change its amount to :amount%
     * @When I do not specify its amount
     */
    public function iSpecifyItsAmountAs($amount = null)
    {
        $this->createPage->specifyAmount($amount);
    }

    /**
     * @When I define it for the :zoneName zone
     * @When I change its zone to :zoneName
     */
    public function iDefineItForTheZone($zoneName)
    {
        $this->createPage->chooseZone($zoneName);
    }

    /**
     * @When I make it applicable for the :taxCategoryName tax category
     * @When I change it to be applicable for the :taxCategoryName tax category
     */
    public function iMakeItApplicableForTheTaxCategory($taxCategoryName)
    {
        $this->createPage->chooseCategory($taxCategoryName);
    }

    /**
     * @When I choose the default tax calculator
     */
    public function iWantToUseTheDefaultTaxCalculator()
    {
        $this->createPage->chooseCalculator('default');
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
     * @Then the tax rate :taxRateName should appear in the registry
     */
    public function theTaxRateShouldAppearInTheRegistry($taxRateName)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isResourceOnPage(['name' => $taxRateName]), 
            sprintf('Tax rate with name %s has not been found.', $taxRateName)
        );
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedItHasBeenSuccessfulCreation()
    {
        $this->notificationChecker->checkCreationNotification(self::RESOURCE_NAME);
    }

    /**
     * @When I delete tax rate :taxRate
     */
    public function iDeletedTaxRate(TaxRateInterface $taxRate)
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['name' => $taxRate->getName()]);
    }

    /**
     * @Then /^(this tax rate) should no longer exist in the registry$/
     */
    public function thisTaxRateShouldNoLongerExistInTheRegistry(TaxRateInterface $taxRate)
    {
        Assert::false(
            $this->indexPage->isResourceOnPage(['code' => $taxRate->getCode()]),
            sprintf('Tax rate with code %s exists but should not.', $taxRate->getCode())
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
     * @Given I want to modify a tax rate :taxRate
     */
    public function iWantToModifyTaxRate(TaxRateInterface $taxRate)
    {
        $this->updatePage->open(['id' => $taxRate->getId()]);
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
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then I should be notified about successful edition
     */
    public function iShouldBeNotifiedAboutSuccessfulEdition()
    {
        $this->notificationChecker->checkEditionNotification(self::RESOURCE_NAME);
    }

    /**
     * @Then /^(this tax rate) name should be "([^"]+)"$/
     * @Then /^(this tax rate) should still be named "([^"]+)"$/
     */
    public function thisTaxRateNameShouldBe(TaxRateInterface $taxRate, $taxRateName)
    {
        $this->assertFieldValue($taxRate, 'name', $taxRateName);
    }

    /**
     * @Then /^(this tax rate) amount should be ([^"]+)%$/
     */
    public function thisTaxRateAmountShouldBe(TaxRateInterface $taxRate, $taxRateAmount)
    {
        $this->assertFieldValue($taxRate, 'amount', $taxRateAmount);
    }

    /**
     * @Then I should be notified that tax rate with this code already exists
     */
    public function iShouldBeNotifiedThatTaxRateWithThisCodeAlreadyExists()
    {
        Assert::true(
            $this->createPage->checkValidationMessageFor('code', 'The tax rate with given code already exists.'),
            'Unique code violation message should appear on page, but it does not.'
        );
    }

    /**
     * @Then there should still be only one tax rate with :element :code
     */
    public function thereShouldStillBeOnlyOneTaxRateWith($element, $code)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isResourceOnPage([$element => $code]),
            sprintf('Tax rate with %s %s cannot be founded.', $element, $code)
        );
    }

    /**
     * @Then /^(this tax rate) should be applicable for the "([^"]+)" tax category$/
     */
    public function thisTaxRateShouldBeApplicableForTaxCategory(TaxRateInterface $taxRate, $taxCategory)
    {
        $this->assertFieldValue($taxRate, 'category', $taxCategory);
    }

    /**
     * @Then /^(this tax rate) should be applicable in "([^"]+)" zone$/
     */
    public function thisTaxRateShouldBeApplicableInZone(TaxRateInterface $taxRate, $zone)
    {
        $this->assertFieldValue($taxRate, 'zone', $zone);
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm($this->createPage, $this->updatePage);

        Assert::true(
            $currentPage->checkValidationMessageFor($element, sprintf('Please enter tax rate %s.', $element)),
            sprintf('Tax rate %s should be required.', $element)
        );
    }

    /**
     * @Then tax rate with :element :name should not be added
     */
    public function taxRateWithElementValueShouldNotBeAdded($element, $name)
    {
        $this->indexPage->open();

        Assert::false(
            $this->indexPage->isResourceOnPage([$element => $name]),
            sprintf('Tax rate with %s %s was created, but it should not.', $element, $name)
        );
    }

    /**
     * @param TaxRateInterface $taxRate
     * @param string $element
     * @param string $taxRateElement
     */
    private function assertFieldValue(TaxRateInterface $taxRate, $element, $taxRateElement)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isResourceOnPage(
                [
                    'code' => $taxRate->getCode(),
                    $element => $taxRateElement,
                ]
            ),
            sprintf('Tax rate %s %s has not been assigned properly.', $element, $taxRateElement)
        );
    }
}
