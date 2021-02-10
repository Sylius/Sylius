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

namespace Sylius\Behat\Context\Api\Admin;

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Webmozart\Assert\Assert;

class ManagingTaxRateContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var IriConverterInterface */
    private $iriConverter;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        IriConverterInterface $iriConverter,
        SharedStorageInterface $sharedStorage
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->iriConverter = $iriConverter;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given I want to create a new tax rate
     */
    public function iWantToCreateANewTaxRate(): void
    {
        $this->client->buildCreateRequest();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(?string $code = null): void
    {
        if ($code) {
            $this->client->addRequestData('code', $code);
        }
    }

    /**
     * @When I name it :name
     * @When I do not name it
     * @When I rename it to :name
     */
    public function iNameIt(?string $name = null): void
    {
        if ($name) {
            $this->client->addRequestData('name', $name);
        }
    }

    /**
     * @When I define it for the :zone zone
     * @When I do not specify its zone
     * @When I change its zone to :zone
     */
    public function iDefineItForTheZone(?ZoneInterface $zone = null): void
    {
        if ($zone) {
            $this->client->addRequestData('zone', $this->iriConverter->getIriFromItem($zone));
        }
    }

    /**
     * @When I make it applicable for the :taxCategory tax category
     * @When I change it to be applicable for the :taxCategory tax category
     * @When I do not specify related tax category
     */
    public function iMakeItApplicableForTheTaxCategory(?TaxCategoryInterface $taxCategory = null): void
    {
        if ($taxCategory) {
            $this->client->addRequestData('category', $this->iriConverter->getIriFromItem($taxCategory));
        }
    }

    /**
     * @When I specify its amount as :amount%
     * @When I do not specify its amount
     */
    public function iSpecifyItsAmountAs(?string $amount = null): void
    {
        if ($amount) {
            $this->client->addRequestData('amount', $amount);
        }
    }

    /**
     * @When I choose the default tax calculator
     */
    public function iChooseTheDefaultTaxCalculator(): void
    {
        $this->client->addRequestData('calculator', 'default');
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
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Tax tax rate could not be created'
        );
    }

    /**
     * @Then the tax rate :taxRate should appear in the registry
     * @Then I should see the tax rate :taxRate in the list
     */
    public function theTaxRateShouldAppearInTheRegistry(TaxRateInterface $taxRate): void
    {
        $name = $taxRate->getName();

        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(), 'name', $name),
            sprintf('Tax rate with name %s does not exists', $name)
        );
    }

    /**
     * @Given I choose "Included in price" option
     */
    public function iChooseOption()
    {
        $this->client->addRequestData('includedInPrice', true);
    }

    /**
     * @Then the tax rate :taxRate should be included in price
     */
    public function theTaxRateShouldIncludePrice(TaxRateInterface $taxRate): void
    {
        Assert::true(
            $taxRate->isIncludedInPrice(),
            sprintf('Tax rate does is not included in price')
        );
    }

    /**
     * @When I delete tax rate :taxRate
     */
    public function iDeleteTaxRate(TaxRateInterface $taxRate): void
    {
        $this->client->delete((string) $taxRate->getId());
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Tax rate could not be deleted'
        );
    }

    /**
     * @Then /^(this tax rate) should no longer exist in the registry$/
     */
    public function thisTaxRateShouldNoLongerExistInTheRegistry(TaxRateInterface $taxRate): void
    {
        $name = $taxRate->getName();

        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(), 'name', $name),
            sprintf('Tax rate with name %s exists', $name)
        );
    }

    /**
     * @When I browse tax rates
     */
    public function iBrowseTaxRates(): void
    {
        $this->client->index();
    }

    /**
     * @When I check the :taxRate tax rate
     * @When I check also the :taxRate tax rate
     */
    public function iCheckTheTaxRate(TaxRateInterface $taxRate): void
    {
        $taxRateToDelete = [];
        if ($this->sharedStorage->has('tax_rate_to_delete')) {
            $taxRateToDelete = $this->sharedStorage->get('tax_rate_to_delete');
        }
        $taxRateToDelete[] = $taxRate->getId();
        $this->sharedStorage->set('tax_rate_to_delete', $taxRateToDelete);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        foreach ($this->sharedStorage->get('tax_rate_to_delete') as $id) {
            $this->client->delete((string) $id)->getContent();
        }
    }

    /**
     * @Then I should be notified that they have been successfully deleted
     */
    public function iShouldBeNotifiedThatTheyHaveBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Tax rate could not be deleted'
        );
    }

    /**
     * @Then I should see a single tax rate in the list
     */
    public function iShouldSeeASingleTaxRateInTheList(): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->index()), 1);
    }

    /**
     * @Then I should be notified that tax rate with this code already exists
     */
    public function iShouldBeNotifiedThatTaxRateWithThisCodeAlreadyExists(): void
    {
        $response = $this->client->getLastResponse();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Tax rate has been created successfully, but it should not'
        );
        Assert::same(
            $this->responseChecker->getError($response),
            'code: The tax rate with given code already exists.'
        );
    }

    /**
     * @Then there should still be only one tax rate with code :code
     */
    public function thereShouldStillBeOnlyOneTaxRateWithCode(string $code): void
    {
        Assert::count(
            $this->responseChecker->getCollectionItemsWithValue($this->client->index(), 'code', $code),
            1,
            sprintf('There is more than one tax rate with code %s', $code)
        );
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatCodeIsRequired(string $element): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('%s: Please enter tax rate %s.', $element, $element)
        );
    }

    /**
     * @Then tax rate with :element :code should not be added
     */
    public function taxRateWithCodeShouldNotBeAdded(string $element, string $code): void
    {
        Assert::false($this->isItemOnIndex($element, $code), sprintf('Tax rate with %s %s exist', $element, $code));
    }

    private function isItemOnIndex(string $property, string $value): bool
    {
        return $this->responseChecker->hasItemWithValue($this->client->index(), $property, $value);
    }

    /**
     * @Then I should be notified that zone has to be selected
     */
    public function iShouldBeNotifiedThatZoneHasToBeSelected(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'zone: Please select tax zone.'
        );
    }

    /**
     * @Then I should be notified that category has to be selected
     */
    public function iShouldBeNotifiedThatCategoryHasToBeSelected(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'category: Please select tax category.'
        );
    }

    /**
     * @When /^I want to modify (this tax rate)$/
     * @When I want to modify a tax rate :taxRate
     */
    public function iWantToModifyThisTaxRate(TaxRateInterface $taxRate): void
    {
        $this->client->buildUpdateRequest((string) $taxRate->getId());

        /* cast amount to string */
        $this->client->addRequestData('amount', (string) $taxRate->getAmount());
    }

    /**
     * @When I remove its amount
     */
    public function iRemoveItsAmount(): void
    {
        $this->client->addRequestData('amount', '');
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->client->update();
    }

    /**
     * @Then /^(this tax rate) amount should still be ([^"]+)%$/
     */
    public function thisTaxRateAmountShouldStillBe(TaxRateInterface $taxRate, string $taxRateAmount): void
    {
        Assert::true($taxRate->getAmount(), $taxRateAmount);
    }

    /**
     * @When I remove its name
     */
    public function iRemoveItsName(): void
    {
        $this->client->addRequestData('name', '');
    }

    /**
     * @Then /^(this tax rate) should still be named "([^"]+)"$/
     * @Then /^(this tax rate) name should be "([^"]*)"$/
     */
    public function thisTaxRateShouldStillBeNamed(TaxRateInterface $taxRate, string $taxRateName): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->show((string) $taxRate->getId()), 'name', $taxRateName),
            sprintf('Tax rate name is not %s', $taxRateName)
        );
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        $this->client->updateRequestData(['code' => 'NEW_CODE']);

        Assert::false($this->responseChecker->hasValue($this->client->update(), 'code', 'NEW_CODE'));
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        Assert::true(
            $this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()),
            'Tax rate could not be edited'
        );
    }

    /**
     * @Then /^(this tax rate) amount should be ([^"]+)%$/
     */
    public function thisTaxRateAmountShouldBe(TaxRateInterface $taxRate, int $taxRateAmount): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->show((string) $taxRate->getId()), 'amount', $taxRateAmount),
            sprintf('Tax rate amount is not %s', $taxRateAmount)
        );
    }

    /**
     * @Then /^(this tax rate) should be applicable for the ("[^"]+" tax category)$/
     */
    public function thisTaxRateShouldBeApplicableForTheTaxCategory(TaxRateInterface $taxRate, TaxCategoryInterface $taxCategory): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->show((string) $taxRate->getId()), 'category', $this->iriConverter->getIriFromItem($taxCategory)),
            sprintf('Tax rate is not applicable for %s tax category', $taxCategory)
        );
    }

    /**
     * @Then /^(this tax rate) should be applicable in ("[^"]+" zone)$/
     */
    public function thisTaxRateShouldBeApplicableInZone(TaxRateInterface $taxRate, ZoneInterface $zone): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->show((string) $taxRate->getId()), 'zone', $this->iriConverter->getIriFromItem($zone)),
            sprintf('Tax rate is not applicable for %s zone', $zone)
        );
    }
}
