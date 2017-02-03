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
use Sylius\Behat\Page\Admin\TaxRate\CreatePageInterface;
use Sylius\Behat\Page\Admin\TaxRate\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ManagingTaxRateContext implements Context
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
     * @When I do not specify its amount
     * @When I remove its amount
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

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $taxRateName]));
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
        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $taxRate->getCode()]));
    }

    /**
     * @Given I want to modify a tax rate :taxRate
     * @Given /^I want to modify (this tax rate)$/
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
        Assert::true($this->updatePage->isCodeDisabled());
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
     * @Then /^(this tax rate) name should be "([^"]+)"$/
     * @Then /^(this tax rate) should still be named "([^"]+)"$/
     */
    public function thisTaxRateNameShouldBe(TaxRateInterface $taxRate, $taxRateName)
    {
        $this->assertFieldValue($taxRate, 'name', $taxRateName);
    }

    /**
     * @Then /^(this tax rate) amount should be ([^"]+)%$/
     * @Then /^(this tax rate) amount should still be ([^"]+)%$/
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
        Assert::same($this->createPage->getValidationMessage('code'), 'The tax rate with given code already exists.');
    }

    /**
     * @Then there should still be only one tax rate with :element :code
     */
    public function thereShouldStillBeOnlyOneTaxRateWith($element, $code)
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage([$element => $code]));
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
     * @Then I should be notified that :element has to be selected
     */
    public function iShouldBeNotifiedThatElementHasToBeSelected($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('Please select tax %s.', $element));
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter tax rate %s.', $element));
    }

    /**
     * @Then tax rate with :element :name should not be added
     */
    public function taxRateWithElementValueShouldNotBeAdded($element, $name)
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $name]));
    }

    /**
     * @When I do not specify its zone
     */
    public function iDoNotSpecifyItsZone()
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When I remove its zone
     */
    public function iRemoveItsZone()
    {
        $this->updatePage->removeZone();
    }

    /**
     * @When I do not specify related tax category
     */
    public function iDoNotSpecifyRelatedTaxCategory()
    {
        // Intentionally left blank to fulfill context expectation
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
            $this->indexPage->isSingleResourceOnPage([
                    'code' => $taxRate->getCode(),
                    $element => $taxRateElement,
            ]),
            sprintf('Tax rate %s %s has not been assigned properly.', $element, $taxRateElement)
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

    /**
     * @Given I choose "Included in price" option
     */
    public function iChooseOption()
    {
        $this->createPage->chooseIncludedInPrice();
    }
}
