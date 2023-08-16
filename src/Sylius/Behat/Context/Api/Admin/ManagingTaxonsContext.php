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

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final class ManagingTaxonsContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When I want to see all taxons in store
     */
    public function iWantToSeeAllTaxonsInStore(): void
    {
        $this->client->index(Resources::TAXONS);
    }

    /**
     * @When I want to create a new taxon
     */
    public function iWantToCreateNewTaxon(): void
    {
        $this->client->buildCreateRequest(Resources::TAXONS);
    }

    /**
     * @When I want to create a new taxon for :parentTaxon
     */
    public function iWantToCreateANewTaxonForParent(TaxonInterface $parentTaxon): void
    {
        $this->iWantToCreateNewTaxon();
        $this->iSetItsParentTaxonTo($parentTaxon);
    }

    /**
     * @When I want to modify the :taxon taxon
     */
    public function iWantToModifyATaxon(TaxonInterface $taxon): void
    {
        $this->sharedStorage->set('taxon', $taxon);

        $this->client->buildUpdateRequest(Resources::TAXONS, $taxon->getCode());
    }

    /**
     * @When I specify its code as :code
     */
    public function iSpecifyItsCodeAs(string $code): void
    {
        $this->client->addRequestData('code', $code);
    }

    /**
     * @When I name it :name in :localeCode
     * @When I rename it to :name in :localeCode
     */
    public function iNameItIn(string $name, string $localeCode): void
    {
        $this->updateTranslations($localeCode, 'name', $name);
    }

    /**
     * @When I set its slug to :slug in :localeCode
     */
    public function iSetItsSlugTo(string $slug, string $localeCode): void
    {
        $this->updateTranslations($localeCode, 'slug', $slug);
    }

    /**
     * @When I enable slug modification
     * @When I enable slug modification in :localeCode
     */
    public function iEnableSlugModification(string $localeCode = 'en_US'): void
    {
        $this->updateTranslations($localeCode, 'slug', '');
    }

    /**
     * @When I describe it as :description in :localeCode
     * @When I change its description to :description in :localeCode
     */
    public function iDescribeItAsIn(string $description, string $localeCode): void
    {
        $this->updateTranslations($localeCode, 'description', $description);
    }

    /**
     * @When I set its parent taxon to :parentTaxon
     * @When I change its parent taxon to :parentTaxon
     */
    public function iSetItsParentTaxonTo(TaxonInterface $parentTaxon): void
    {
        $this->client->addRequestData('parent', $this->iriConverter->getIriFromItemInSection($parentTaxon, 'admin'));
    }

    /**
     * @When I (try to) add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I remove taxon named :name
     * @When I delete taxon named :name
     * @When I try to delete taxon named :name
     */
    public function iRemoveTaxonNamed(string $name): void
    {
        $code = StringInflector::nameToLowercaseCode($name);

        $this->client->delete(Resources::TAXONS, $code);
    }

    /**
     * @When I move down :taxonName taxon
     */
    public function iMoveDownTaxon(string $taxonName): void
    {
        $lastResponse = $this->client->getLastResponse();
        $code = StringInflector::nameToLowercaseCode($taxonName);

        $taxon = $this->responseChecker->getCollectionItemsWithValue($lastResponse, 'code', $code);
        $position = $taxon[0]['position'];

        $this->client->buildUpdateRequest(Resources::TAXONS, $code);
        $this->client->addRequestData('position', $position + 1);

        $this->client->update();
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Taxon could not be created',
        );
    }

    /**
     * @Then I should see the taxon named :name in the list
     */
    public function iShouldSeeTheTaxonNamedInTheList(string $name): void
    {
        $code = StringInflector::nameToLowercaseCode($name);

        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::TAXONS), 'code', $code),
        );
    }

    /**
     * @Then /^taxon named "([^"]+)" should not be added$/
     * @Then the taxon named :name should no longer exist in the registry
     */
    public function taxonNamedShouldNotBeAdded(string $name): void
    {
        $code = StringInflector::nameToLowercaseCode($name);

        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::TAXONS), 'code', $code),
        );
    }

    /**
     * @Then /^the ("[^"]+" taxon) should appear in the registry$/
     */
    public function theTaxonShouldAppearInTheRegistry(TaxonInterface $taxon): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::TAXONS), 'code', $taxon->getCode()),
        );

        $this->sharedStorage->set('taxon', $taxon);
    }

    /**
     * @Then I should be notified that I cannot delete a menu taxon of any channel
     */
    public function iShouldBeNotifiedThatICannotDeleteAMenuTaxonOfAnyChannel(): void
    {
        $lastResponse = $this->client->getLastResponse();

        Assert::false($this->responseChecker->isDeletionSuccessful($lastResponse));
    }

    /**
     * @Then /^(it) should not belong to any other taxon$/
     */
    public function itShouldNotBelongToAnyOtherTaxon(TaxonInterface $taxon): void
    {
        Assert::true($this->responseChecker->hasItemWithValues(
            $this->client->getLastResponse(),
            [
                'code' => $taxon->getCode(),
                'parent' => null,
            ],
        ));
    }

    /**
     * @Then /^(this taxon) should (belongs to "[^"]+")$/
     */
    public function thisTaxonShouldBelongsTo(TaxonInterface $taxon, TaxonInterface $parentTaxon): void
    {
        $this->iWantToSeeAllTaxonsInStore();

        Assert::true($this->responseChecker->hasItemWithValues(
            $this->client->getLastResponse(),
            [
                'code' => $taxon->getCode(),
                'parent' => $this->iriConverter->getIriFromItemInSection($parentTaxon, 'admin'),
            ],
        ));
    }

    /**
     * @Then I should see :count taxons on the list
     */
    public function iShouldSeeTaxonsInTheList(int $count): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $count);
    }

    /**
     * @Then this taxon :field should be :value
     * @Then this taxon should have :field :value in :localeCode
     */
    public function thisTaxonFieldShouldBe(string $field, string $value, string $localeCode = 'en_US'): void
    {
        Assert::true($this->responseChecker->hasTranslation($this->client->getLastResponse(), $localeCode, $field, $value));
    }

    /**
     * @Then the :field of the :taxonName taxon should( still) be :value
     */
    public function theFieldOfTheTaxonShouldStillBe(string $field, string $taxonName, string $value): void
    {
        $this->thisTaxonFieldShouldBe($field, $value);
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        $this->client->updateRequestData(['code' => 'NEW_CODE']);

        Assert::false(
            $this->responseChecker->hasValue($this->client->update(), 'code', 'NEW_CODE'),
            'The code field with value NEW_CODE exist',
        );
    }

    private function updateTranslations(string $localeCode, string $field, string $value): void
    {
        $this->client->updateRequestData([
            'translations' => [
                $localeCode => [
                    'locale' => $localeCode,
                    $field => $value,
                ],
            ],
        ]);
    }
}
