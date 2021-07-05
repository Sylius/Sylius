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

namespace Sylius\Tests\Api\Admin;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ProductVariantsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_denies_access_to_a_products_list_for_not_authenticated_user(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'product/product_variant.yaml']);

        $this->client->request('GET', '/api/v2/admin/product-variants');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function it_gets_all_product_variants(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'product/product_variant.yaml', 'authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/product-variants',
            server: $header,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/product_variant/get_product_variants_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_a_product_variant(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['channel.yaml', 'product/product_variant.yaml', 'authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/product-variants/%s', $productVariant->getCode()),
            server: $header,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/product_variant/get_product_variant_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_updates_channel_pricing_of_product_variant(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['channel.yaml', 'product/product_variant.yaml', 'authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant'];
        /** @var ChannelInterface $channel */
        $channel = $fixtures['channel_web'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/product-variants/%s', $productVariant->getCode()),
            server: $header,
            content: json_encode([
                'channelPricings' => ['WEB' => [
                    '@id' => sprintf('/api/v2/admin/channel-pricings/%s', $productVariant->getChannelPricingForChannel($channel)->getId()),
                    'price' => 3000,
                    'originalPrice' => 4000,
                ]]
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_variant/put_product_variant_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_product_variant_enabled_by_default(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['channel.yaml', 'product/product_variant.yaml', 'authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductInterface $product */
        $product = $fixtures['product'];

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/product-variants',
            server: $header,
            content: json_encode([
                'code' => 'MUG_2',
                'position' => 1,
                'product' => sprintf('/api/v2/admin/products/%s', $product->getCode()),
                'channelPricings' => ['WEB' => [
                    'channelCode' => 'WEB',
                    'price' => 4000,
                    'originalPrice' => 5000,
                    'minimumPrice' => 2000,
                ]],
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_variant/post_product_variant_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_creates_disabled_product_variant(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['channel.yaml', 'product/product_variant.yaml', 'authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductInterface $product */
        $product = $fixtures['product'];

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/product-variants',
            server: $header,
            content: json_encode([
                'code' => 'MUG_2',
                'position' => 1,
                'product' => sprintf('/api/v2/admin/products/%s', $product->getCode()),
                'channelPricings' => ['WEB' => [
                    'channelCode' => 'WEB',
                    'price' => 4000,
                    'originalPrice' => 5000,
                    'minimumPrice' => 2000,
                ]],
                'enabled' => false,
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_variant/post_product_variant_disabled_response',
            Response::HTTP_CREATED,
        );
    }
}
