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
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class CreatingProductVariantContext implements Context
{
    public function __construct(private ApiClientInterface $client, private IriConverterInterface $iriConverter)
    {
    }

    /**
     * @When /^I create a new "([^"]+)" variant priced at ("[^"]+") for ("[^"]+" product) in the ("[^"]+" channel)$/
     */
    public function iCreateANewVariantPricedAtForProductInTheChannel(
        string $name,
        int $price,
        ProductInterface $product,
        ChannelInterface $channel,
    ): void {
        $this->client->buildCreateRequest(Resources::PRODUCT_VARIANTS);
        $this->client->addRequestData('product', $this->iriConverter->getIriFromResource($product));
        $this->client->addRequestData('code', StringInflector::nameToCode($name));

        $this->client->addRequestData('channelPricings', [
            $channel->getCode() => [
                'price' => $price,
                'channelCode' => $channel->getCode(),
            ],
        ]);

        $this->client->create();
    }
}
