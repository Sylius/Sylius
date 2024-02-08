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
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Webmozart\Assert\Assert;

final class BrowsingCatalogPromotionProductVariantsContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
    ) {
    }

    /**
     * @When I browse variants affected by catalog promotion :catalogPromotion
     */
    public function iBrowseVariantsAffectedByCatalogPromotion(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->client->index(Resources::PRODUCT_VARIANTS);
        $this->client->addFilter('catalogPromotion', $this->iriConverter->getIriFromItem($catalogPromotion));
        $this->client->filter();
    }

    /**
     * @Then /^there should be (\d+) product variants? on the list$/
     */
    public function thereShouldBeProductVariantsOnTheList(int $count): void
    {
        Assert::same(
            $this->responseChecker->countCollectionItems($this->client->getLastResponse()),
            $count,
        );
    }

    /**
     * @Then it should be the :variantName product variant
     * @Then it should be :firstVariant and :secondVariant product variants
     */
    public function theProductVariantShouldBeInTheRegistry(string ...$variantsNames): void
    {
        foreach ($variantsNames as $variantName) {
            Assert::true($this->responseChecker->hasItemWithTranslation(
                $this->client->getLastResponse(),
                'en_US',
                'name',
                $variantName,
            ));
        }
    }
}
