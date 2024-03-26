<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Behat\Context\Ui\Admin\Helper\ValidationTrait;
use Sylius\Behat\Element\Admin\TaxRate\FilterElementInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\TaxRate\CreatePageInterface;
use Sylius\Behat\Page\Admin\TaxRate\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Webmozart\Assert\Assert;

final class ManagingTaxRateContext implements Context
{
    use ValidationTrait;

    public function __construct(
        private IndexPageInterface $indexPage,
        private CreatePageInterface $createPage,
        private UpdatePageInterface $updatePage,
        private CurrentPageResolverInterface $currentPageResolver,
        private FilterElementInterface $filterElement,
    ) {
    }

    /**
     * @When I want to create a new tax rate
     */
    public function iWantToCreateNewTaxRate()
    {
        $this->createPage->open();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(?string $code = null): void
    {
        $this->createPage->specifyCode($code ?? '');
    }

    /**
     * @When /^I specify its amount as ([^"]+)%$/
     * @When I do not specify its amount
     * @When I remove its amount
     */
    public function iSpecifyItsAmountAs($amount = null)
    {
        $this->createPage->specifyAmount($amount ?? '');
    }

    /**
     * @When I make it start at :startDate and end at :endDate
     */
    public function iMakeItStartAtAndEndAt(string $startDate, string $endDate): void
    {
        $this->createPage->specifyStartDate(new \DateTime($startDate));
        $this->createPage->specifyEndDate(new \DateTime($endDate));
    }

    /**
     * @When I set the start date to :startDate
     */
    public function iSetTheStartDateTo(string $startDate): void
    {
        $this->createPage->specifyStartDate(new \DateTime($startDate));
    }

    /**
     * @When I set the end date to :endDate
     */
    public function iSetTheEndDateTo(string $endDate): void
    {
        $this->createPage->specifyStartDate(new \DateTime($endDate));
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
        $this->createPage->nameIt($name ?? '');
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
     * @Then I should see the tax rate :taxRateName in the list
     * @Then the tax rate :taxRateName should appear in the registry
     */
    public function theTaxRateShouldAppearInTheRegistry(string $taxRateName): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $taxRateName]));
    }

    /**
     * @Then the tax rate :taxRate should be included in price
     */
    public function theTaxRateShouldIncludePrice(TaxRateInterface $taxRate): void
    {
        $this->updatePage->open(['id' => $taxRate->getId()]);

        Assert::true(
            $taxRate->isIncludedInPrice(),
            sprintf('Tax rate is not included in price'),
        );
    }

    /**
     * @Then I should not see a tax rate with name :name
     */
    public function iShouldNotSeeATaxRateWithName(string $taxRateName): void
    {
        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['name' => $taxRateName]),
            sprintf('Tax rate with name "%s" has been found, but should not.', $taxRateName),
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
        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $taxRate->getCode()]));
    }

    /**
     * @When I want to modify a tax rate :taxRate
     * @When /^I want to modify (this tax rate)$/
     */
    public function iWantToModifyTaxRate(TaxRateInterface $taxRate)
    {
        $this->updatePage->open(['id' => $taxRate->getId()]);
    }

    /**
     * @Then the code field should be disabled
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
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
     * @Then I should be notified that :element is invalid
     */
    public function iShouldBeNotifiedThatIsInvalid(string $element): void
    {
        $this->assertFieldValidationMessage($element, sprintf('The tax rate %s is invalid.', $element));
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
     * @When I check (also) the :taxRateName tax rate
     */
    public function iCheckTheTaxRate(string $taxRateName): void
    {
        $this->indexPage->checkResourceOnPage(['name' => $taxRateName]);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @When I browse tax rates
     */
    public function iWantToBrowseTaxRates(): void
    {
        $this->indexPage->open();
    }

    /**
     * @Then I should see a single tax rate in the list
     */
    public function iShouldSeeASingleTaxRateInTheList(): void
    {
        Assert::same($this->indexPage->countItems(), 1);
    }

    /**
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
            sprintf('Tax rate %s %s has not been assigned properly.', $element, $taxRateElement),
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

    /**
     * @Then I should be notified that tax rate should not end before it starts
     */
    public function iShouldBeNotifiedThatTaxRateShouldNotEndBeforeItStarts(): void
    {
        $this->assertFieldValidationMessage('end_date', 'The tax rate should not end before it starts');
    }

    /**
     * @When /^I filter tax rates by (end|start) date from "(\d{4}-\d{2}-\d{2})"$/
     */
    public function iFilterTaxRatesByDateFrom(string $dateType, string $date): void
    {
        $this->filterElement->specifyDateFrom($dateType, $date);
        $this->filterElement->filter();
    }

    /**
     * @When /^I filter tax rates by (end|start) date up to "(\d{4}-\d{2}-\d{2})"$/
     */
    public function iFilterTaxRatesByDateUpTo(string $dateType, string $date): void
    {
        $this->filterElement->specifyDateTo($dateType, $date);
        $this->filterElement->filter();
    }

    /**
     * @When /^I filter tax rates by (end|start) date from "(\d{4}-\d{2}-\d{2})" up to "(\d{4}-\d{2}-\d{2})"$/
     */
    public function iFilterTaxRatesByDateFromDateToDate(string $dateType, string $fromDate, string $toDate): void
    {
        $this->filterElement->specifyDateFrom($dateType, $fromDate);
        $this->filterElement->specifyDateTo($dateType, $toDate);
        $this->filterElement->filter();
    }

    protected function resolveCurrentPage(): SymfonyPageInterface
    {
        return $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);
    }
}
