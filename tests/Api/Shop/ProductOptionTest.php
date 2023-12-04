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

use Sylius\Tests\Api\JsonApiTestCase;

final class ProductOptionTest extends JsonApiTestCase
{
    /** @test */
    public function it_returns_product_option(): void
    {
        $this->loadFixturesFromFile('product/product_with_many_locales.yaml');

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/product-options/COLOR',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/product/get_product_option');
    }

    /** @test */
    public function it_returns_product_option_value(): void
    {
        $this->loadFixturesFromFile('product/product_with_many_locales.yaml');

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/product-option-values/COLOR_RED',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/product/get_product_option_value');
    }
}
