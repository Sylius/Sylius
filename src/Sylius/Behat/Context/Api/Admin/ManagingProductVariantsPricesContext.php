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
use Sylius\Behat\Context\Api\Resources;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ManagingProductVariantsPricesContext implements Context
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    /**
     * @When /^I change the price of the ("[^"]+" product variant) to ("[^"]+") in ("[^"]+" channel)$/
     */
    public function iChangeThePriceOfTheProductVariantInChannel(
        ProductVariantInterface $variant,
        int $price,
        ChannelInterface $channel,
    ): void {
        $this->updateChannelPricingField($variant, $channel, $price, 'price');
    }

    /**
     * @When /^I change the original price of the ("[^"]+" product variant) to ("[^"]+") in ("[^"]+" channel)$/
     */
    public function iChangeTheOriginalPriceOfTheProductVariantInChannel(
        ProductVariantInterface $variant,
        int $originalPrice,
        ChannelInterface $channel,
    ): void {
        $this->updateChannelPricingField($variant, $channel, $originalPrice, 'originalPrice');
    }

    /**
     * @When /^I remove the original price of the ("[^"]+" product variant) in ("[^"]+" channel)$/
     */
    public function iRemoveTheOriginalPriceOfTheProductVariantInChannel(
        ProductVariantInterface $variant,
        ChannelInterface $channel,
    ): void {
        $this->updateChannelPricingField($variant, $channel, null, 'originalPrice');
    }

    private function updateChannelPricingField(
        ProductVariantInterface $variant,
        ChannelInterface $channel,
        ?int $price,
        string $field,
    ): void {
        $this->client->buildUpdateRequest(Resources::PRODUCT_VARIANTS, $variant->getCode());

        $content = $this->client->getContent();
        $content['channelPricings'][$channel->getCode()][$field] = $price;
        $this->client->updateRequestData($content);

        $this->client->update();
    }
}
