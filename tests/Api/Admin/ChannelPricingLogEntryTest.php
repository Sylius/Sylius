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

use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ChannelPricingLogEntryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ChannelPricingLogEntryTest extends JsonApiTestCase
{
    /** @test */
    public function it_denies_access_to_a_channel_pricing_log_entries_for_not_authenticated_user(): void
    {
        $this->loadFixturesFromFiles(['product/product_variant_with_lowest_price.yaml']);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/channel-pricing-log-entries',
            server: $this->headerBuilder()->withJsonLdAccept()->build(),
        );

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function it_gets_single_channel_pricing_log_entry(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_variant_with_lowest_price.yaml']);

        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $fixtures['channel_pricing_product_variant_mug_blue_home'];

        /** @var RepositoryInterface $channelPricingLogEntryRepository */
        $channelPricingLogEntryRepository = $this->getContainer()->get('sylius.repository.channel_pricing_log_entry');
        /** @var ChannelPricingLogEntryInterface $firstLogEntry */
        $channelPricingLogEntry = $channelPricingLogEntryRepository->findOneBy(['channelPricing' => $channelPricing]);

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/channel-pricing-log-entries/%d', $channelPricingLogEntry->getId()),
            server: $this->headerBuilder()->withJsonLdAccept()->withAdminUserAuthorization('api@example.com')->build(),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/get_channel_pricing_log_entry_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_all_channel_pricing_log_entries(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_variant_with_lowest_price.yaml']);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/channel-pricing-log-entries',
            server: $this->headerBuilder()->withJsonLdAccept()->withAdminUserAuthorization('api@example.com')->build(),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/get_channel_pricing_log_entries_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_filtered_channel_pricing_log_entries(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_variant_with_lowest_price.yaml']);

        $uri = '/api/v2/admin/channel-pricing-log-entries';
        $uri .= '?channelPricing.channelCode=' . $fixtures['channel_home']->getCode();
        $uri .= '&channelPricing.productVariant.code=' . $fixtures['product_variant_mug_blue']->getCode();

        $this->client->request(
            method: 'GET',
            uri: $uri,
            server: $this->headerBuilder()->withJsonLdAccept()->withAdminUserAuthorization('api@example.com')->build(),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/get_filtered_channel_pricing_log_entries_response',
            Response::HTTP_OK,
        );
    }
}
