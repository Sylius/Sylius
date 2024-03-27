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

namespace Sylius\Behat\Context\Api\Shop;

use ApiPlatform\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final class TaxonContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
        private ObjectManager $objectManager,
    ) {
    }

    /**
     * @When /^I try to browse products from (taxon "([^"]+)")$/
     * @When /^I check the ("[^"]+" taxon)'s details$/
     */
    public function iTryToBrowseProductsFrom(TaxonInterface $taxon): void
    {
        $this->objectManager->clear(); // avoiding doctrine cache
        $this->client->show(Resources::TAXONS, $taxon->getCode());
    }

    /**
     * @Then I should not see :taxon in the vertical menu
     */
    public function iShouldNotSeeInTheVerticalMenu(TaxonInterface $taxon): void
    {
        Assert::false(
            $this->isTaxonChildVisible($taxon),
            sprintf('Taxon %s is in the vertical menu, but it should not.', $taxon->getName()),
        );
    }

    /**
     * @Then I should see the taxon name :name
     */
    public function iShouldSeeTaxonName(string $name): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->getLastResponse(), 'name', $name),
            sprintf('Taxon with name %s does not exist.', $name),
        );
    }

    /**
     * @Then /^I should see ("([^"]+)" and "([^"]+)" in the vertical menu)$/
     */
    public function iShouldSeeInTheVerticalMenu(iterable $taxons): void
    {
        foreach ($taxons as $taxon) {
            Assert::true(
                $this->isTaxonChildVisible($taxon),
                sprintf('Taxon %s is not in the vertical menu, but it should be.', $taxon->getName()),
            );
        }
    }

    private function isTaxonChildVisible(TaxonInterface $taxon): bool
    {
        $taxonIri = $this->iriConverter->getIriFromResource($taxon);
        $response = $this->client->getLastResponse();
        $children = $this->responseChecker->getValue($response, 'children');

        return in_array($taxonIri, $children, true);
    }
}
