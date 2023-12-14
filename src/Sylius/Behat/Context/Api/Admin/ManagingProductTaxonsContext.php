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
use Sylius\Behat\Context\Api\Resources;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final class ManagingProductTaxonsContext implements Context
{
    public function __construct(private ApiClientInterface $client, private IriConverterInterface $iriConverter)
    {
    }

    /**
     * @When I assign the :taxon taxon to the :product product
     * @When I (try to) add :taxon taxon to the :product product
     */
    public function iAddTaxonToTheProduct(ProductInterface $product, TaxonInterface $taxon): void
    {
        $this->client->buildCreateRequest(Resources::PRODUCT_TAXONS);
        $this->client->addRequestData('taxon', $this->iriConverter->getIriFromResource($taxon));
        $this->client->addRequestData('product', $this->iriConverter->getIriFromResource($product));
        $this->client->create();
    }

    /**
     * @When I try to assign an empty taxon to the :product product
     */
    public function iTryToAssignAnEmptyTaxonToTheProduct(ProductInterface $product): void
    {
        $this->client->buildCreateRequest(Resources::PRODUCT_TAXONS);
        $this->client->addRequestData('product', $this->iriConverter->getIriFromResource($product));
        $this->client->create();
    }

    /**
     * @When I try to assign an empty product to the :taxon taxon
     */
    public function iTryToAssignAnEmptyProductToTheTaxon(TaxonInterface $taxon): void
    {
        $this->client->buildCreateRequest(Resources::PRODUCT_TAXONS);
        $this->client->addRequestData('taxon', $this->iriConverter->getIriFromResource($taxon));
        $this->client->create();
    }

    /**
     * @When /^I try to assign the product taxon of (product "[^"]+") and (taxon "[^"]+") to the (product "[^"]+")$/
     */
    public function iTryToAssignTheProductTaxonOfProductAndTaxonToTheProduct(
        ProductInterface $productTaxonProduct,
        TaxonInterface $productTaxonTaxon,
    ): void {
        $this->iAddTaxonToTheProduct($productTaxonProduct, $productTaxonTaxon);
    }

    /**
     * @When I change that the :product product does not belong to the :taxon taxon
     */
    public function iChangeThatTheProductDoesNotBelongToTheTaxon(
        ProductInterface $product,
        TaxonInterface $taxon,
    ): void {
        $productTaxon = $product->getProductTaxons()->filter(
            fn (ProductTaxonInterface $productTaxon) => $taxon === $productTaxon->getTaxon(),
        )->first();

        $this->client->delete(Resources::PRODUCT_TAXONS, (string) $productTaxon->getId());
    }

    /**
     * @Then I should be notified that specifying a :part is required
     */
    public function iShouldBeNotifiedThatSpecifyingAIsRequired(string $part): void
    {
        Assert::contains(
            $this->client->getLastResponse()->getContent(),
            sprintf('Please select a %s.', $part),
        );
    }

    /**
     * @Then I should be notified that product taxons cannot be duplicated
     */
    public function iShouldBeNotifiedThatProductTaxonsCannotBeDuplicated(): void
    {
        Assert::contains(
            $this->client->getLastResponse()->getContent(),
            'Product taxons cannot be duplicated.',
        );
    }
}
