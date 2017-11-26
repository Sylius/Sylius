<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\TaxRate\CreatePageInterface;
use Sylius\Behat\Page\Admin\TaxRate\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Webmozart\Assert\Assert;

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
    public function iWantToCreateNewTaxRate(): void
    {
        $this->createPage->open();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null): void
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I specify its amount as :amount%
     * @When I do not specify its amount
     * @When I remove its amount
     */
    public function iSpecifyItsAmountAs($amount = null): void
    {
        $this->createPage->specifyAmount($amount);
    }

    /**
     * @When I define it for the :zoneName zone
     * @When I change its zone to :zoneName
     */
    public function iDefineItForTheZone($zoneName): void
    {
        $this->createPage->chooseZone($zoneName);
    }

    /**
     * @When I make it applicable for the :taxCategoryName tax category
     * @When I change it to be applicable for the :taxCategoryName tax category
     */
    public function iMakeItApplicableForTheTaxCategory($taxCategoryName): void
    {
        $this->createPage->chooseCategory($taxCategoryName);
    }

    /**
     * @When I choose the default tax calculator
     */
    public function iWantToUseTheDefaultTaxCalculator(): void
    {
        $this->createPage->chooseCalculator('default');
    }

    /**
     * @When I name it :name
     * @When I rename it to :name
     * @When I do not name it
     * @When I remove its name
     */
    public function iNameIt($name = null): void
    {
        $this->createPage->nameIt($name);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
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
     * @When I delete tax rate :taxRate
     */
    public function iDeletedTaxRate(TaxRateInterface $taxRate): void
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['name' => $taxRate->getName()]);
    }

    /**
     * @Then /^(this tax rate) should no longer exist in the registry$/
     */
    public function thisTaxRateShouldNoLongerExistInTheRegistry(TaxRateInterface $taxRate): void
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $taxRate->getCode()]));
    }

    /**
     * @Given I want to modify a tax rate :taxRate
     * @Given /^I want to modify (this tax rate)$/
     */
    public function iWantToModifyTaxRate(TaxRateInterface $taxRate): void
    {
        $this->updatePage->open(['id' => $taxRate->getId()]);
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled(): void
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then /^(this tax rate) name should be "([^"]+)"$/
     * @Then /^(this tax rate) should still be named "([^"]+)"$/
     */
    public function thisTaxRateNameShouldBe(TaxRateInterface $taxRate, $taxRateName): void
    {
        $this->assertFieldValue($taxRate, 'name', $taxRateName);
    }

    /**
     * @Then /^(this tax rate) amount should be ([^"]+)%$/
     * @Then /^(this tax rate) amount should still be ([^"]+)%$/
     */
    public function thisTaxRateAmountShouldBe(TaxRateInterface $taxRate, $taxRateAmount): void
    {
        $this->assertFieldValue($taxRate, 'amount', $taxRateAmount);
    }

    /**
     * @Then I should be notified that tax rate with this code already exists
     */
    public function iShouldBeNotifiedThatTaxRateWithThisCodeAlreadyExists(): void
    {
        Assert::same($this->createPage->getValidationMessage('code'), 'The tax rate with given code already exists.');
    }

    /**
     * @Then there should still be only one tax rate with :element :code
     */
    public function thereShouldStillBeOnlyOneTaxRateWith($element, $code): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage([$element => $code]));
    }

    /**
     * @Then /^(this tax rate) should be applicable for the "([^"]+)" tax category$/
     */
    public function thisTaxRateShouldBeApplicableForTaxCategory(TaxRateInterface $taxRate, $taxCategory): void
    {
        $this->assertFieldValue($taxRate, 'category', $taxCategory);
    }

    /**
     * @Then /^(this tax rate) should be applicable in "([^"]+)" zone$/
     */
    public function thisTaxRateShouldBeApplicableInZone(TaxRateInterface $taxRate, $zone): void
    {
        $this->assertFieldValue($taxRate, 'zone', $zone);
    }

    /**
     * @Then I should be notified that :element has to be selected
     */
    public function iShouldBeNotifiedThatElementHasToBeSelected($element): void
    {
        $this->assertFieldValidationMessage($element, sprintf('Please select tax %s.', $element));
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element): void
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter tax rate %s.', $element));
    }

    /**
     * @Then tax rate with :element :name should not be added
     */
    public function taxRateWithElementValueShouldNotBeAdded($element, $name): void
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $name]));
    }

    /**
     * @When I do not specify its zone
     */
    public function iDoNotSpecifyItsZone(): void
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When I remove its zone
     */
    public function iRemoveItsZone(): void
    {
        $this->updatePage->removeZone();
    }

    /**
     * @When I do not specify related tax category
     */
    public function iDoNotSpecifyRelatedTaxCategory(): void
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
    private function assertFieldValue(TaxRateInterface $taxRate, string $element, string $taxRateElement): void
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
    private function assertFieldValidationMessage(string $element, string $expectedMessage): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage($element), $expectedMessage);
    }

    /**
     * @Given I choose "Included in price" option
     */
    public function iChooseOption(): void
    {
        $this->createPage->chooseIncludedInPrice();
    }
}
