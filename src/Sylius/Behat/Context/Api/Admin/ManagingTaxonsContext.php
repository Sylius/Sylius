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

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class ManagingTaxonsContext implements Context
{
    public function __construct(
        private RequestStack $requestStack,
        private ResponseCheckerInterface $responseChecker,
        private ApiClientInterface $apiClient,
    ) {
    }

    /**
     * @When I remove taxon named :name
     * @When I delete taxon named :name
     * @When I try to delete taxon named :name
     */
    public function iRemoveTaxonNamed(string $name): void
    {
        $code = StringInflector::nameToLowercaseCode($name);

        $this->apiClient->delete(Resources::TAXONS, $code);
    }

    /**
     * @Then /^taxon named "([^"]+)" should not be added$/
     * @Then the taxon named :name should no longer exist in the registry
     */
    public function taxonNamedShouldNotBeAdded(string $name): void
    {
        $code = StringInflector::nameToLowercaseCode($name);

        Assert::false(
            $this->responseChecker->hasItemWithValue($this->apiClient->index(Resources::TAXONS), 'code', $code),
        );
    }

    /**
     * @Then /^the ("[^"]+" taxon) should appear in the registry$/
     */
    public function theTaxonShouldAppearInTheRegistry(TaxonInterface $taxon): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->apiClient->index(Resources::TAXONS), 'code', $taxon->getCode()),
        );
    }

    /**
     * @Then I should be notified that I cannot delete a menu taxon of any channel
     */
    public function iShouldBeNotifiedThatICannotDeleteAMenuTaxonOfAnyChannel(): void
    {
        $lastResponse = $this->apiClient->getLastResponse();

        Assert::false($this->responseChecker->isDeletionSuccessful($lastResponse));
    }

    /**
     * @When I want to see all taxons in store
     */
    public function iWantToSeeAllTaxonsInStore(): void
    {
        $this->apiClient->index(Resources::TAXONS);
    }

    /**
     * @When I move down :taxonName taxon
     */
    public function iMoveDownTaxon(string $taxonName): void
    {
        $lastResponse = $this->apiClient->getLastResponse();
        $code = StringInflector::nameToLowercaseCode($taxonName);

        $taxon = $this->responseChecker->getCollectionItemsWithValue($lastResponse, 'code', $code);
        $position = $taxon[0]['position'];

        $this->apiClient->buildUpdateRequest(Resources::TAXONS, $code);
        $this->apiClient->addRequestData('position', $position + 1);

        $this->apiClient->update();
    }
}
