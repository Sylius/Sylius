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

final class ProductsTest extends JsonApiTestCase
{
    /** @test */
    public function it_preserves_query_param_when_redirecting_from_product_slug_to_product_code(): void
    {
        $this->loadFixturesFromFile('product/product_variant_with_original_price.yaml');

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/products-by-slug/mug?paramName=paramValue',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertEquals('/api/v2/shop/products/MUG?paramName=paramValue', $response->headers->get(('Location')));
        $this->assertResponseCode($response, Response::HTTP_MOVED_PERMANENTLY);
    }

    /** @test */
    public function it_returns_product_with_translations_in_default_locale(): void
    {
        $fixtures = $this->loadFixturesFromFile('product/product_with_many_locales.yaml');

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];
        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/products/%s', $product->getCode()),
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product/get_product_with_default_locale_translation',
            Response::HTTP_OK,
        );
    }

    /**
     * @test
     *
     * @dataProvider getGermanLocales
     */
    public function it_returns_product_with_translations_in_locale_from_header(string $germanLocale): void
    {
        $fixtures = $this->loadFixturesFromFile('product/product_with_many_locales.yaml');

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];
        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/products/%s', $product->getCode()),
            server: [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
                'HTTP_ACCEPT_LANGUAGE' => $germanLocale,
            ],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product/get_product_with_de_DE_locale_translation',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_returns_products_collection(): void
    {
        $this->loadFixturesFromFiles(['product/product_variant_with_original_price.yaml']);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/products',
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product/get_products_collection_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_returns_products_collection_with_only_available_associations(): void
    {
        $this->loadFixturesFromFile('product/products_with_associations.yaml');

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/products',
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product/get_products_collection_with_associations_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_returns_product_item_with_only_available_associations(): void
    {
        $this->loadFixturesFromFile('product/products_with_associations.yaml');

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/products/MUG',
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product/get_product_with_default_locale_translation',
            Response::HTTP_OK,
        );
    }

    /**
     * @test
     *
     * @dataProvider getPolishLocales
     */
    public function it_returns_product_attributes_collection_with_translations_in_locale_from_header(
        string $polishLocale,
    ): void {
        $this->loadFixturesFromFiles(['channel.yaml', 'product/product_attribute.yaml']);

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/products/%s/attributes', 'MUG_SW'),
            server: array_merge(self::CONTENT_TYPE_HEADER, ['HTTP_ACCEPT_LANGUAGE' => $polishLocale]),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product/get_product_attributes_collection_response',
        );
    }

    /** @test */
    public function it_returns_paginated_attributes_collection(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'product/product_attribute.yaml']);

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/products/%s/attributes', 'MUG_SW'),
            parameters: ['itemsPerPage' => 2],
            server: array_merge(self::CONTENT_TYPE_HEADER, ['HTTP_ACCEPT_LANGUAGE' => 'pl_PL']),
        );

        $this->assertCount(2, json_decode($this->client->getResponse()->getContent(), true)['hydra:member']);
    }

    public function getGermanLocales(): iterable
    {
        yield ['de_DE']; // Locale code syntax
        yield ['de-DE']; // RFC 4647 and RFC 3066
        yield ['DE-DE']; // RFC 4647 and RFC 3066
        yield ['de-de']; // RFC 4647 and RFC 3066
    }

    public function getPolishLocales(): iterable
    {
        yield ['pl_PL']; // Locale code syntax
        yield ['pl-PL']; // RFC 4647 and RFC 3066
        yield ['PL-PL']; // RFC 4647 and RFC 3066
        yield ['pl-pl']; // RFC 4647 and RFC 3066
    }
}
