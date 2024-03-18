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

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class BrowsingProductVariantsContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
    ) {
    }

    /**
     * @When I start sorting variants by position
     */
    public function iSortProductsByPosition(): void
    {
        $this->client->index(
            Resources::PRODUCT_VARIANTS,
            [
                'order[position]' => 'desc',
            ],
        );
    }

    /**
     * @When I set the position of :productVariant to :position
     */
    public function iSetThePositionOfTo(ProductVariantInterface $productVariant, int $position): void
    {
        $this->client->buildUpdateRequest(Resources::PRODUCT_VARIANTS, $productVariant->getCode());
        $this->client->updateRequestData(['position' => $position]);
    }

    /**
     * @When I save my new configuration
     */
    public function iSaveMyNewConfiguration(): void
    {
        $this->client->update();
    }

    /**
     * @Then the first variant in the list should have name :variantName
     */
    public function theFirstVariantInTheListShouldHaveName(string $variantName): void
    {
        $variants = $this->responseChecker->getCollection($this->client->getLastResponse());

        $firstVariant = reset($variants);

        $this->assertProductVariantName($firstVariant['translations']['en_US']['name'], $variantName);
    }

    /**
     * @Then the last variant in the list should have name :variantName
     */
    public function theLastVariantInTheListShouldHaveName(string $variantName): void
    {
        $variants = $this->responseChecker->getCollection($this->client->getLastResponse());

        $lastVariant = end($variants);

        $this->assertProductVariantName($lastVariant['translations']['en_US']['name'], $variantName);
    }

    private function assertProductVariantName(string $variantName, string $expectedVariantName): void
    {
        Assert::same(
            $variantName,
            $expectedVariantName,
            sprintf(
                'Expected product variant to have name "%s", but it is named "%s".',
                $expectedVariantName,
                $variantName,
            ),
        );
    }
}
