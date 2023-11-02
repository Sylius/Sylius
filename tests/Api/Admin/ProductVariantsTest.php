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

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ProductVariantsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_denies_access_to_a_product_variants_list_for_not_authenticated_user(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);

        $this->client->request('GET', '/api/v2/admin/product-variants');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function it_gets_all_product_variants(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);
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
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);
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
    public function it_creates_a_product_variant_with_all_optional_data(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/product-variants',
            server: $header,
            content: json_encode([
                'code' => 'MUG_RED',
                'product' => '/api/v2/admin/products/MUG_SW',
                'optionValues' => ['/api/v2/admin/product-option-values/COLOR_RED'],
                'channelPricings' => ['WEB' => [
                    'channelCode' => 'WEB',
                    'price' => 4000,
                    'originalPrice' => 5000,
                    'minimumPrice' => 2000,
                ]],
                'translations' => [
                    'en_US' => [
                        'locale' => 'en_US',
                        'name' => 'Red mug',
                    ],
                ],
                'enabled' => false,
                'position' => 1,
                'tracked' => true,
                'onHold' => 5,
                'onHand' => 10,
                'weight' => 100.5,
                'width' => 100.5,
                'height' => 100.5,
                'depth' => 100.5,
                'taxCategory' => '/api/v2/admin/tax-categories/default',
                'shippingCategory' => '/api/v2/admin/shipping-categories/default',
                'shippingRequired' => true,
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_variant/post_product_variant_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_creates_a_product_variant_enabled_by_default(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/product-variants',
            server: $header,
            content: json_encode([
                'code' => 'MUG_3',
                'product' => '/api/v2/admin/products/MUG_SW',
                'channelPricings' => ['WEB' => [
                    'channelCode' => 'WEB',
                    'price' => 4000,
                ]],
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_variant/post_product_variant_enabled_by_default_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_does_not_allow_to_create_product_variant_with_invalid_channel_code(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/product-variants',
            server: $header,
            content: json_encode([
                'code' => 'CUP',
                'product' => '/api/v2/admin/products/MUG_SW',
                'channelPricings' => ['NON-EXISTING-CHANNEL' => [
                    'channelCode' => 'NON-EXISTING-CHANNEL',
                    'price' => 4000,
                ]],
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function it_does_not_allow_to_create_product_variant_without_channel_code(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/product-variants',
            server: $header,
            content: json_encode([
                'code' => 'CUP',
                'product' => '/api/v2/admin/products/MUG_SW',
                'channelPricings' => [
                    'NON-EXISTING-CHANNEL' => ['price' => 4000]
                ],
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function it_does_not_allow_to_create_product_variant_without_product(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/product-variants',
            server: $header,
            content: json_encode([
                'code' => 'CUP',
                'channelPricings' => ['WEB' => [
                    'channelCode' => 'WEB',
                    'price' => 4000,
                ]],
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function it_does_not_allow_to_create_product_variant_with_invalid_locale_code(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/product-variants',
            server: $header,
            content: json_encode([
                'code' => 'CUP',
                'product' => '/api/v2/admin/products/MUG_SW',
                'channelPricings' => ['WEB' => [
                    'channelCode' => 'WEB',
                    'price' => 4000,
                    'originalPrice' => 5000,
                    'minimumPrice' => 2000,
                ]],
                'translations' => [
                    'NON-EXISTING-LOCALE-CODE' => [
                        'locale' => 'NON-EXISTING-LOCALE-CODE',
                        'name' => 'Yellow mug',
                    ],
                ],
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function it_updates_the_existing_product_variant(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);
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
                'optionValues' => ['/api/v2/admin/product-option-values/COLOR_RED'],
                'channelPricings' => ['WEB' => [
                    '@id' => sprintf('/api/v2/admin/channel-pricings/%s', $productVariant->getChannelPricingForChannel($channel)->getId()),
                    'price' => 3000,
                    'originalPrice' => 4000,
                    'minimumPrice' => 500,
                ]],
                'translations' => [
                    'pl_PL' => [
                        '@id' => sprintf('/api/v2/admin/product-variant-translations/%s', $productVariant->getTranslation('pl_PL')->getId()),
                        'locale' => 'pl_PL',
                        'name' => 'Czerwony kubek',
                    ],
                    'de_DE' => [
                        'locale' => 'de_DE',
                        'name' => 'Rote Tasse',
                    ],
                ],
                'enabled' => false,
                'position' => 2,
                'tracked' => false,
                'onHold' => 0,
                'onHand' => 0,
                'weight' => 50.5,
                'width' => 50.5,
                'height' => 50.5,
                'depth' => 50.5,
                'taxCategory' => '/api/v2/admin/tax-categories/special',
                'shippingCategory' => '/api/v2/admin/shipping-categories/special',
                'shippingRequired' => false,
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_variant/put_product_variant_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_does_not_allow_to_update_product_variant_with_invalid_locale_code(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/product-variants/%s', $productVariant->getCode()),
            server: $header,
            content: json_encode([
                'code' => 'CUP',
                'channelPricings' => ['WEB' => [
                    'channelCode' => 'WEB',
                    'price' => 4000,
                    'originalPrice' => 5000,
                    'minimumPrice' => 2000,
                ]],
                'translations' => [
                    'NON-EXISTING-LOCALE-CODE' => [
                        'locale' => 'NON-EXISTING-LOCALE-CODE',
                        'name' => 'Yellow mug',
                    ],
                ],
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function it_deletes_the_product_variant(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant_2'];

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/product-variants/%s', $productVariant->getCode()),
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function it_does_not_delete_the_product_variant_in_use(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant'];

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/product-variants/%s', $productVariant->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_variant/delete_product_variant_in_use_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }
}
