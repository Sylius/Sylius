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
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class ProductVariantContext implements Context
{
    private ApiClientInterface $client;

    private ResponseCheckerInterface $responseChecker;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
    }

    /**
     * @When I select :variant variant
     */
    public function iSelectVariant(ProductVariantInterface $variant): void
    {
        $this->client->show($variant->getCode());
    }

    /**
     * @When I view variants
     */
    public function iViewVariants(): void
    {
        $this->client->index();
    }

    /**
     * @Then /^the product variant price should be ("[^"]+")$/
     */
    public function theProductVariantPriceShouldBe(int $price): void
    {
        $response = $this->responseChecker->getResponseContent($this->client->getLastResponse());

        Assert::same($response['price'], $price);
    }

    /**
     * @Then /^the product original price should be ("[^"]+")$/
     */
    public function theProductOriginalPriceShouldBe(int $originalPrice): void
    {
        $response = $this->responseChecker->getResponseContent($this->client->getLastResponse());

        Assert::same($response['originalPrice'], $originalPrice);
    }

    /**
     * @Then /^I should see ("[^"]+" variant) is discounted from ("[^"]+") to ("[^"]+") with "([^"]+)" promotion$/
     */
    public function iShouldSeeVariantIsDiscountedFromToWithPromotion(
        ProductVariantInterface $targetVariant,
        int $originalPrice,
        int $price,
        string $promotionName
    ): void {
        $response = $this->responseChecker->getResponseContent($this->client->getLastResponse());

        foreach ($response['hydra:member'] as $variant) {
            if ($variant['code'] !== $targetVariant->getCode()) {
                continue;
            }

            Assert::same($variant['price'], $price);
            Assert::same($variant['originalPrice'], $originalPrice);
            Assert::inArray(['name' => $promotionName], $variant['appliedPromotions']);
        }
    }

    /**
     * @Then I should see :firstVariant variant and :secondVariant variant are not discounted
     */
    public function iShouldSeeVariantsAreNotDiscounted(string ...$variantsNames): void
    {
        $response = $this->responseChecker->getResponseContent($this->client->getLastResponse());

        foreach ($response['hydra:member'] as $variant) {
            if (in_array($variant['translations']['en_US']['name'], $variantsNames)) {
                Assert::keyNotExists($variant, 'appliedPromotions');
            }
        }
    }
}
