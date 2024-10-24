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

use ApiPlatform\Metadata\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final class ManagingProductTaxonsContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private IriConverterInterface $iriConverter,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When /^I am browsing the (\d+)(?:st|nd|rd|th) page of products from ("([^"]+)" taxon)$/
     * @When /^I go to the (\d+)(?:st|nd|rd|th) page of products from ("([^"]+)" taxon)$/
     */
    public function iAmBrowsingThePageOfProductsFromTaxon(int $page, TaxonInterface $taxon): void
    {
        $this->iAmBrowsingProductsFromTaxon($taxon);
        $this->client->addFilter('page', $page);
        $this->client->filter();

        $this->sharedStorage->set('response', $this->client->getLastResponse());
    }

    /**
     * @When /^I am browsing products from ("([^"]+)" taxon)$/
     */
    public function iAmBrowsingProductsFromTaxon(TaxonInterface $taxon): void
    {
        $this->client->index(Resources::PRODUCT_TAXONS);
        $this->client->addFilter('taxon.code', $taxon->getCode());
        $this->client->addFilter('itemsPerPage', 10);
        $this->client->filter();

        $this->sharedStorage->set('response', $this->client->getLastResponse());
    }

    /**
     * @When I filter them by :product product
     */
    public function iFilterThemByProduct(ProductInterface $product): void
    {
        $this->client->addFilter('product.code', $product->getCode());
        $this->client->filter();

        $this->sharedStorage->set('response', $this->client->getLastResponse());
    }

    /**
     * @When I set the position of :product to :position
     */
    public function iSetThePositionOfProductTo(ProductInterface $product, int|string $position): void
    {
        $this->client->buildUpdateRequest(Resources::PRODUCT_TAXONS, (string) $product->getProductTaxons()->current()->getId());
        $this->client->updateRequestData(['position' => is_numeric($position) ? (int) $position : $position]);
    }

    /**
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
     * @When I sort this taxon's products :order by :field
     */
    public function iSortProductsBy(string $order, string $field): void
    {
        $this->client->sort([$field => ManagingProductsContext::SORT_TYPES[$order]]);
        $this->sharedStorage->set('response', $this->client->getLastResponse());
    }

    /**
     * @When I save my new configuration
     */
    public function iSaveMyNewConfiguration(): void
    {
        $this->client->update();
        $this->sharedStorage->set('response', $this->client->getLastResponse());
    }

    /**
     * @Then /^the (first|last) product on the list within this taxon should have name "([^"]+)"$/
     */
    public function theLastProductOnTheListWithinThisTaxonShouldHaveName(string $position, string $name): void
    {
        $productTaxons = $this->responseChecker->getCollection($this->sharedStorage->get('response'));
        $productTaxon = $position === 'last' ? end($productTaxons) : reset($productTaxons);

        /** @var ProductInterface $product */
        $product = $this->iriConverter->getResourceFromIri($productTaxon['product']);

        Assert::same($product->getTranslation()->getName(), $name);
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

    /**
     * @Then I should be notified that the position :position is invalid
     */
    public function iShouldBeNotifiedThatThePositionIsInvalid(): void
    {
        Assert::contains(
            (string) $this->responseChecker->getError($this->client->getLastResponse()),
            'The type of the "position" attribute must be "int", "string" given.',
        );
    }

    /**
     * @Then I should see the :taxon taxon
     */
    public function iShouldSeeTheTaxon(TaxonInterface $taxon): void
    {
        Assert::true(
            $this->isTaxonVisible($taxon),
            sprintf('Taxon with code %s does not exist, but it should', $taxon->getCode()),
        );
    }

    /**
     * @Then I should see the :product product
     */
    public function iShouldSeeTheProduct(ProductInterface $product): void
    {
        Assert::true(
            $this->isProductVisible($product),
            sprintf('Product with code %s does not exist, but it should', $product->getCode()),
        );
    }

    /**
     * @Then I should not see the :taxon taxon
     */
    public function iShouldNotSeeTheTaxon(TaxonInterface $taxon): void
    {
        Assert::false(
            $this->isTaxonVisible($taxon),
            sprintf('Taxon with code %s exists, but it should not', $taxon->getCode()),
        );
    }

    /**
     * @Then I should not see the :product product
     */
    public function iShouldNotSeeTheProduct(ProductInterface $product): void
    {
        Assert::false(
            $this->isProductVisible($product),
            sprintf('Product with code %s does not exist, but it should not', $product->getCode()),
        );
    }

    private function isTaxonVisible(TaxonInterface $taxon): bool
    {
        return in_array($this->iriConverter->getIriFromResource($taxon), array_column($this->responseChecker->getCollection($this->sharedStorage->get('response')), 'taxon'));
    }

    private function isProductVisible(ProductInterface $product): bool
    {
        return in_array($this->iriConverter->getIriFromResource($product), array_column($this->responseChecker->getCollection($this->sharedStorage->get('response')), 'product'));
    }
}
