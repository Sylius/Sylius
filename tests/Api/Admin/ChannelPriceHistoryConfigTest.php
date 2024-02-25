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

namespace Sylius\Tests\Api\Admin;

use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ChannelPriceHistoryConfigTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_a_channel_price_history_config(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxonomy.yaml']);

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/channel-price-history-configs/%d', $fixtures['web_price_history_config']->getId()),
            server: $this->headerBuilder()->withJsonLdAccept()->withAdminUserAuthorization('api@example.com')->build(),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/channel_price_history_config/get_channel_price_history_config_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_updates_a_channel_price_history_config(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxonomy.yaml']);
        /** @var TaxonInterface $brandTaxon */
        $brandTaxon = $fixtures['brand_taxon'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/channel-price-history-configs/%d', $fixtures['web_price_history_config']->getId()),
            server: $this->headerBuilder()->withJsonLdContentType()->withJsonLdAccept()->withAdminUserAuthorization('api@example.com')->build(),
            content: json_encode([
                'lowestPriceForDiscountedProductsVisible' => true,
                'lowestPriceForDiscountedProductsCheckingPeriod' => 60,
                'taxonsExcludedFromShowingLowestPrice' => [
                    sprintf('/api/v2/admin/taxons/%s', $brandTaxon->getCode()),
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/channel_price_history_config/put_channel_price_history_config_response',
            Response::HTTP_OK,
        );
    }
}
