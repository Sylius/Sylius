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
use Sylius\Behat\Service\Accessor\NotificationAccessorInterface;
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
     * @var NotificationAccessorInterface
     */
    private $notificationAccessor;

    /**
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param NotificationAccessorInterface $notificationAccessor
     */
    public function __construct(
        IndexPageInterface $indexPage, 
        CreatePageInterface $createPage, 
        UpdatePageInterface $updatePage, 
        NotificationAccessorInterface $notificationAccessor
    ) {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->notificationAccessor = $notificationAccessor;
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
     */
    public function iSpecifyItsAmountAs($amount)
    {
        $this->createPage->specifyAmount($amount);
    }

    /**
     * @When I define it for the :zoneName zone
     */
    public function iDefineItForTheZone($zoneName)
    {
        $this->createPage->chooseZone($zoneName);
    }

    /**
     * @When I make it applicable for the :taxCategoryName tax category
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
        Assert::true(
            $this->notificationAccessor->hasSuccessMessage(), 
            'Message type is not positive.'
        );

        Assert::true(
            $this->notificationAccessor->isSuccessfullyCreatedFor(self::RESOURCE_NAME), 
            'Successful creation message does not appear.'
        );
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
        Assert::true(
            $this->notificationAccessor->hasSuccessMessage(),
            'Message type is not positive.'
        );

        Assert::true(
            $this->notificationAccessor->isSuccessfullyDeletedFor(self::RESOURCE_NAME),
            'Successful deletion message does not appear.'
        );
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
        Assert::true(
            $this->notificationAccessor->hasSuccessMessage(),
            'Message type is not positive.'
        );

        Assert::true(
            $this->notificationAccessor->isSuccessfullyUpdatedFor(self::RESOURCE_NAME),
            'Successful edition message does not appear.'
        );
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
