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

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\Request;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\Converter\AdminToShopIriConverterInterface;
use Sylius\Bundle\ApiBundle\Provider\ApiPathPrefixProviderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var AdminToShopIriConverterInterface */
    private $adminToShopIriConverter;

    /** @var ApiPathPrefixProviderInterface */
    private $apiPathPrefixProvider;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        AdminToShopIriConverterInterface $adminToShopIriConverter,
        ApiPathPrefixProviderInterface $apiPathPrefixProvider
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->adminToShopIriConverter = $adminToShopIriConverter;
        $this->apiPathPrefixProvider = $apiPathPrefixProvider;
    }

    /**
     * @When /^I check (this product)'s details$/
     * @When I view product :product
     */
    public function iOpenProductPage(ProductInterface $product): void
    {
        $this->client->show($product->getSlug());
    }

    /**
     * @Then I should see the product name :name
     */
    public function iShouldSeeProductName(string $name): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithTranslation(
                $this->client->getLastResponse(),
                'en_US',
                'name',
                $name
            )
        );

        Assert::same($this->responseChecker->getTranslationValue($this->client->getLastResponse(), 'name'), $name);
    }

    /**
     * @Then its current variant should be named :variantName
     */
    public function itsCurrentVariantShouldBeNamed(string $variantName): void
    {
        $response = $this->client->getLastResponse();

        $productVariant = $this->responseChecker->getValue($response, 'variants');
        $this->client->executeCustomRequest(
            Request::custom($this->adminToShopIriConverter->convert($productVariant[0]), HttpRequest::METHOD_GET)
        );

        Assert::true(
            $this->responseChecker->hasTranslation(
                $this->client->getLastResponse(),
                'en_US',
                'name',
                $variantName
            )
        );
    }

    /**
     * @Then /^I should see (shop) as main iri identifier on this product$/
     */
    public function iShouldSeeShopAsMainIriIdentifierOnThisProduct(string $prefixType): void
    {
        $iri = $this->responseChecker->getValue($this->client->getLastResponse(), '@id');
        Assert::same($this->apiPathPrefixProvider->getPathPrefix($iri), $prefixType);
    }

    /**
     * @Then /^I should see (shop) as variants iri identifiers on this product$/
     */
    public function iShouldSeeShopAsVariantsIriIdentifiersOnThisProduct(string $prefixType): void
    {
        foreach ($this->responseChecker->getValue($this->client->getLastResponse(), 'variants') as $variant) {
            Assert::same($this->apiPathPrefixProvider->getPathPrefix($variant), $prefixType);
        }
    }

    /**
     * @Then /^I should see (shop) as translations iri identifiers on this product$/
     */
    public function iShouldSeeShopAsTranslationsIriIdentifiersOnThisProduct(string $prefixType): void
    {
        $iri = $this->responseChecker->getValue($this->client->getLastResponse(), 'translations')['en_US']['@id'];
        Assert::same($this->apiPathPrefixProvider->getPathPrefix($iri), $prefixType);
    }

    /**
     * @Then /^I should see (shop) as images iri identifiers on this product$/
     */
    public function iShouldSeeShopAsImagesIriIdentifiersOnThisProduct(string $prefixType): void
    {
        $images = $this->responseChecker->getValue($this->client->getLastResponse(), 'images');
        Assert::notEmpty($images);

        foreach ($images as $image) {
            Assert::same($this->apiPathPrefixProvider->getPathPrefix($image['@id']), $prefixType);
        }
    }

    /**
     * @Then /^I should see (shop) as product taxon iri identifiers on this product$/
     */
    public function iShouldSeeShopAsProductTaxonIriIdentifiersOnThisProduct(string $prefixType): void
    {
        $this->checkIriPrefixOnArray($this->client->getLastResponse(), 'productTaxons', $prefixType);
    }

    /**
     * @Then /^I should see (shop) as main taxon iri identifiers on this product$/
     */
    public function iShouldSeeShopAsMainTaxonIriIdentifiersOnThisProduct(string $prefixType): void
    {
        $iri = $this->responseChecker->getValue($this->client->getLastResponse(), 'mainTaxon');
        Assert::same($this->apiPathPrefixProvider->getPathPrefix($iri), $prefixType);
    }

    /**
     * @Then /^I should see (shop) as product review iri identifiers on this product$/
     */
    public function iShouldSeeShopAsProductReviewIriIdentifiersOnThisProduct(string $prefixType): void
    {
        $this->checkIriPrefixOnArray($this->client->getLastResponse(), 'reviews', $prefixType);
    }

    /**
     * @Then /^I should see (shop) as product options iri identifiers on this product$/
     */
    public function iShouldSeeShopAsProductOptionsIriIdentifiersOnThisProduct(string $prefixType): void
    {
        $this->checkIriPrefixOnArray($this->client->getLastResponse(), 'options', $prefixType);
    }

    private function checkIriPrefixOnArray(Response $response, string $fieldType, string $prefixType): void
    {
        $fields = $this->responseChecker->getValue($response, $fieldType);
        Assert::notEmpty($fields);

        foreach ($fields as $productTaxon) {
            Assert::same($this->apiPathPrefixProvider->getPathPrefix($productTaxon), $prefixType);
        }
    }
}
