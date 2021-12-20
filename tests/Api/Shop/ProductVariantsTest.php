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

        $this->client->request('GET', '/api/v2/shop/product-variants/MUG_BLUE', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/product/get_product_variant_with_original_price_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_products_without_original_price(): void
    {
        $this->loadFixturesFromFile('product/product_variant_with_original_price.yaml');

        $this->client->request('GET', '/api/v2/shop/product-variants/MUG_RED', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/product/get_product_variant_with_price_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_product_variant_with_translations(): void
    {
        $fixtures = $this->loadFixturesFromFile('product/product_with_many_locales.yaml');

        /** @var ProductInterface $product */
        $product = $fixtures['product_variant_mug_blue'];
        $this->client->request('GET',
            sprintf('/api/v2/shop/product-variants/%s', $product->getCode()),
            [],
            [],
            self::CONTENT_TYPE_HEADER
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product/get_product_variant_with_default_locale_translation',
            Response::HTTP_OK
        );
    }
}
