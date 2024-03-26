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

namespace Sylius\Behat\Context\Api\Admin;

use ApiPlatform\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Admin\Helper\ValidationTrait;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Webmozart\Assert\Assert;

class ManagingTaxRatesContext implements Context
{
    use ValidationTrait;

    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When I want to create a new tax rate
     */
    public function iWantToCreateANewTaxRate(): void
    {
        $this->client->buildCreateRequest(Resources::TAX_RATES);
    }

    /**
     * @When I specify its code as :code
     */
    public function iSpecifyItsCodeAs(string $code): void
    {
        $this->client->addRequestData('code', $code);
    }

    /**
     * @When I name it :name
     * @When I rename it to :name
     */
    public function iNameIt(string $name): void
    {
        $this->client->addRequestData('name', $name);
    }

    /**
     * @When I define it for the :zone zone
     * @When I change its zone to :zone
     */
    public function iDefineItForTheZone(ZoneInterface $zone): void
    {
        $this->client->addRequestData('zone', $this->iriConverter->getIriFromResource($zone));
    }

    /**
     * @When I make it applicable for the :taxCategory tax category
     * @When I change it to be applicable for the :taxCategory tax category
     */
    public function iMakeItApplicableForTheTaxCategory(TaxCategoryInterface $taxCategory): void
    {
        $this->client->addRequestData('category', $this->iriConverter->getIriFromResource($taxCategory));
    }

    /**
     * @When I specify its amount as :amount%
     */
    public function iSpecifyItsAmountAs(string $amount): void
    {
        $this->client->addRequestData('amount', $amount);
    }

    /**
     * @When I do not specify related tax category
     * @When I do not specify its zone
     * @When I do not name it
     * @When I do not specify its code
     */
    public function iDoNotSpecifyItsField(): void
    {
        // Intentionally left blank
    }

    /**
     * @When I choose the default tax calculator
     */
    public function iChooseTheDefaultTaxCalculator(): void
    {
        $this->client->addRequestData('calculator', 'default');
    }

    /**
     * @When I make it start at :startDate and end at :endDate
     */
    public function iMakeItStartAtAndEndAt(string $startDate, string $endDate): void
    {
        $this->client->addRequestData('startDate', $startDate);
        $this->client->addRequestData('endDate', $endDate);
    }

    /**
     * @When I set the start date to :startDate
     */
    public function iSetTheStartDateTo(string $startDate): void
    {
        $this->client->addRequestData('startDate', $startDate);
    }

    /**
     * @When I set the end date to :endDate
     */
    public function iSetTheEndDateTo(string $endDate): void
    {
        $this->client->addRequestData('endDate', $endDate);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I choose "Included in price" option
     */
    public function iChooseOption()
    {
        $this->client->addRequestData('includedInPrice', true);
    }

    /**
     * @When /^I want to modify (this tax rate)$/
     * @When I want to modify a tax rate :taxRate
     */
    public function iWantToModifyThisTaxRate(TaxRateInterface $taxRate): void
    {
        $this->client->buildUpdateRequest(Resources::TAX_RATES, (string) $taxRate->getCode());
        $this->client->addRequestData('amount', (string) $taxRate->getAmount());
    }

    /**
     * @When I browse tax rates
     */
    public function iBrowseTaxRates(): void
    {
        $this->client->index(Resources::TAX_RATES);
    }

    /**
     * @When I remove its name
     */
    public function iRemoveItsName(): void
    {
        $this->client->addRequestData('name', '');
    }

    /**
     * @When I filter tax rates by start date from :startDate
     */
    public function iFilterTaxRatesByStartDateFrom(string $startDate): void
    {
        $this->client->addFilter('startDate[after]', $startDate);
        $this->client->filter();
    }

    /**
     * @When I filter tax rates by start date up to :startDate
     */
    public function iFilterTaxRatesByStartDateUpTo(string $startDate): void
    {
        $this->client->addFilter('startDate[before]', $startDate);
        $this->client->filter();
    }

    /**
     * @When I filter tax rates by start date from :startDate up to :endDate
     */
    public function iFilterTaxRatesByStartDateFromUpTo(string $startDate, string $endDate): void
    {
        $this->client->addFilter('startDate[after]', $startDate);
        $this->client->addFilter('startDate[before]', $endDate);
        $this->client->filter();
    }

    /**
     * @When I filter tax rates by end date from :endDate
     */
    public function iFilterTaxRatesByEndDateFrom(string $endDate): void
    {
        $this->client->addFilter('endDate[after]', $endDate);
        $this->client->filter();
    }

    /**
     * @When I filter tax rates by end date up to :endDate
     */
    public function iFilterTaxRatesByEndDateUpTo(string $endDate): void
    {
        $this->client->addFilter('endDate[before]', $endDate);
        $this->client->filter();
    }

    /**
     * @When I filter tax rates by end date from :startDate up to :endDate
     */
    public function iFilterTaxRatesByEndDateFromUpTo(string $startDate, string $endDate): void
    {
        $this->client->addFilter('endDate[after]', $startDate);
        $this->client->addFilter('endDate[before]', $endDate);
        $this->client->filter();
    }

    /**
     * @When I delete tax rate :taxRate
     */
    public function iDeleteTaxRate(TaxRateInterface $taxRate): void
    {
        $this->client->delete(Resources::TAX_RATES, (string) $taxRate->getCode());
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Tax tax rate could not be created',
        );
    }

    /**
     * @Then the tax rate :taxRate should appear in the registry
     * @Then I should see the tax rate :taxRate in the list
     */
    public function theTaxRateShouldAppearInTheRegistry(TaxRateInterface $taxRate): void
    {
        $this->sharedStorage->set('tax_rate', $taxRate);

        $name = $taxRate->getName();

        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::TAX_RATES), 'name', $name),
            sprintf('Tax rate with name %s does not exist', $name),
        );
    }

    /**
     * @Then the tax rate :taxRate should be included in price
     */
    public function theTaxRateShouldIncludePrice(TaxRateInterface $taxRate): void
    {
        Assert::true(
            $taxRate->isIncludedInPrice(),
            sprintf('Tax rate is not included in price'),
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Tax rate could not be deleted',
        );
    }

    /**
     * @Then /^(this tax rate) should no longer exist in the registry$/
     */
    public function thisTaxRateShouldNoLongerExistInTheRegistry(TaxRateInterface $taxRate): void
    {
        $name = $taxRate->getName();

        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::TAX_RATES), 'name', $name),
            sprintf('Tax rate with name %s exists', $name),
        );
    }

    /**
     * @Then I should see a single tax rate in the list
     */
    public function iShouldSeeASingleTaxRateInTheList(): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->index(Resources::TAX_RATES)), 1);
    }

    /**
     * @Then I should be notified that tax rate with this code already exists
     */
    public function iShouldBeNotifiedThatTaxRateWithThisCodeAlreadyExists(): void
    {
        $response = $this->client->getLastResponse();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Tax rate has been created successfully, but it should not',
        );
        Assert::same(
            $this->responseChecker->getError($response),
            'code: The tax rate with given code already exists.',
        );
    }

    /**
     * @Then there should still be only one tax rate with code :code
     */
    public function thereShouldStillBeOnlyOneTaxRateWithCode(string $code): void
    {
        Assert::count(
            $this->responseChecker->getCollectionItemsWithValue($this->client->index(Resources::TAX_RATES), 'code', $code),
            1,
            sprintf('There is more than one tax rate with code %s', $code),
        );
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatElementIsRequired(string $element): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('%s: Please enter tax rate %s.', $element, $element),
        );
    }

    /**
     * @Then tax rate with :element :code should not be added
     */
    public function taxRateWithCodeShouldNotBeAdded(string $element, string $code): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::TAX_RATES), $element, $code),
            sprintf('Tax rate with %s %s exist', $element, $code),
        );
    }

    /**
     * @Then I should be notified that zone has to be selected
     */
    public function iShouldBeNotifiedThatZoneHasToBeSelected(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'zone: Please select tax zone.',
        );
    }

    /**
     * @Then I should be notified that category has to be selected
     */
    public function iShouldBeNotifiedThatCategoryHasToBeSelected(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'category: Please select tax category.',
        );
    }

    /**
     * @Then I should not see a tax rate with name :name
     */
    public function iShouldNotSeeATaxRateWithName(string $name): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'name', $name),
            sprintf('Tax rate with name %s exists', $name),
        );
    }

    /**
     * @Then /^(this tax rate) should still be named "([^"]+)"$/
     * @Then /^(this tax rate) name should be "([^"]*)"$/
     */
    public function thisTaxRateShouldStillBeNamed(TaxRateInterface $taxRate, string $taxRateName): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->show(Resources::TAX_RATES, (string) $taxRate->getCode()), 'name', $taxRateName),
            sprintf('Tax rate name is not %s', $taxRateName),
        );
    }

    /**
     * @Then the code field should be disabled
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        $this->client->updateRequestData(['code' => 'NEW_CODE']);

        Assert::false($this->responseChecker->hasValue($this->client->update(), 'code', 'NEW_CODE'));
    }

    /**
     * @Then /^(this tax rate) amount should be ([^"]+)%$/
     */
    public function thisTaxRateAmountShouldBe(TaxRateInterface $taxRate, int $taxRateAmount): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->show(Resources::TAX_RATES, (string) $taxRate->getCode()), 'amount', $taxRateAmount),
            sprintf('Tax rate amount is not %s', $taxRateAmount),
        );
    }

    /**
     * @Then /^(this tax rate) should be applicable for the ("[^"]+" tax category)$/
     */
    public function thisTaxRateShouldBeApplicableForTheTaxCategory(TaxRateInterface $taxRate, TaxCategoryInterface $taxCategory): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->show(Resources::TAX_RATES, (string) $taxRate->getCode()), 'category', $this->iriConverter->getIriFromResource($taxCategory)),
            sprintf('Tax rate is not applicable for %s tax category', $taxCategory),
        );
    }

    /**
     * @Then /^(this tax rate) should be applicable in ("[^"]+" zone)$/
     */
    public function thisTaxRateShouldBeApplicableInZone(TaxRateInterface $taxRate, ZoneInterface $zone): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->show(Resources::TAX_RATES, (string) $taxRate->getCode()), 'zone', $this->iriConverter->getIriFromResource($zone)),
            sprintf('Tax rate is not applicable for %s zone', $zone),
        );
    }

    /**
     * @Then I should be notified that amount is invalid
     */
    public function iShouldBeNotifiedThatAmountIsInvalid(): void
    {
        Assert::true(
            $this->responseChecker->hasViolationWithMessage(
                $this->client->getLastResponse(),
                'The tax rate amount is invalid.',
                'amount',
            ),
        );
    }

    /**
     * @Then I should be notified that tax rate should not end before it starts
     */
    public function iShouldBeNotifiedThatTaxRateShouldNotEndBeforeItStarts(): void
    {
        Assert::true(
            $this->responseChecker->hasViolationWithMessage(
                $this->client->getLastResponse(),
                'The tax rate should not end before it starts',
                'endDate',
            ),
        );
    }
}
