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

namespace Sylius\Tests\Api\Shop;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductVariantsTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_products_with_original_price(): void
    {
        $this->loadFixturesFromFile('product/product_variant_with_original_price.yaml');

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/product-variants/MUG_BLUE',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse(
            $response,
            'shop/product_variant/get_product_variant_with_original_price_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_products_without_original_price(): void
    {
        $this->loadFixturesFromFile('product/product_variant_with_original_price.yaml');

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/product-variants/MUG_RED',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/product_variant/get_product_variant_with_price_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_returns_product_variant_with_translations(): void
    {
        $fixtures = $this->loadFixturesFromFile('product/product_with_many_locales.yaml');

        /** @var ProductInterface $product */
        $product = $fixtures['product_variant_mug_blue'];
        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/product-variants/%s', $product->getCode()),
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product_variant/get_product_variant_with_default_locale_translation',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_returns_product_variant_with_applied_promotion(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'catalog_promotion/catalog_promotion.yaml',
            'catalog_promotion/product_variant.yaml',
        ]);

        /** @var ProductInterface $product */
        $product = $fixtures['product_variant'];
        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/product-variants/%s', $product->getCode()),
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product_variant/get_product_variant_with_applied_promotion',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_returns_nothing_if_variant_not_found(): void
    {
        $this->loadFixturesFromFile('product/product_with_many_locales.yaml');

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/product-variants/NON_EXISTING_VARIANT',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /** @test */
    public function it_gets_product_variants(): void
    {
        $this->loadFixturesFromFile('product/product_variant_with_original_price.yaml');

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/product-variants',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse(
            $response,
            'shop/product_variant/get_product_variants_response',
            Response::HTTP_OK,
        );
    }
}
