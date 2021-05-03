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
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var AdminToShopIriConverterInterface */
    private $adminToShopIriConverter;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        AdminToShopIriConverterInterface $adminToShopIriConverter
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->adminToShopIriConverter = $adminToShopIriConverter;
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
     * @When I browse products
     */
    public function iViewProducts(): void
    {
        $this->client->index();
    }

    /**
     * @When /^I should see only (\d+) product(s)$/
     */
    public function iShouldSeeOnlyProducts(int $count): void
    {
        Assert::same(
            count($this->responseChecker->getCollection($this->client->getLastResponse())),
            $count,
            'Number of products from response is different then expected'
        );
    }

    /**
     * @Then I should not see the product with name :name
     */
    public function iShouldNotSeeProductWithName(string $name): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithTranslation(
                $this->client->getLastResponse(),
                'en_US',
                'name',
                $name
            )
        );
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
}
