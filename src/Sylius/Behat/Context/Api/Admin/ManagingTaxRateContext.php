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
use Behat\Behat\Tester\Exception\PendingException;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
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

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        IriConverterInterface $iriConverter
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->iriConverter = $iriConverter;
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
     */
    public function iSpecifyItsCodeAs(string $code): void
    {
        $this->client->addRequestData('code', $code);
    }

    /**
     * @When I name it :name
     */
    public function iNameIt(string $name): void
    {
        $this->client->addRequestData('name', $name);
    }

    /**
     * @When I define it for the :zone zone
     */
    public function iDefineItForTheZone(ZoneInterface $zone): void
    {
        $this->client->addRequestData('zone', $this->iriConverter->getIriFromItem($zone));
    }

    /**
     * @When I make it applicable for the :taxCategory tax category
     */
    public function iMakeItApplicableForTheTaxCategory(TaxCategoryInterface $taxCategory): void
    {
        $this->client->addRequestData('category', $this->iriConverter->getIriFromItem($taxCategory));
    }

    /**
     * @When I specify its amount as :amount%
     */
    public function iSpecifyItsAmountAs(int $amount): void
    {
        $this->client->addRequestData('amount', $amount);
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
}
