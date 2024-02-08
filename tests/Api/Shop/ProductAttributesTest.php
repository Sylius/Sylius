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
use Symfony\Component\HttpFoundation\Response;

final class ProductAttributesTest extends JsonApiTestCase
{
    /** @test */
    public function it_returns_product_attribute_with_translations_in_default_locale(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'product/product_attribute.yaml']);

        $this->client->request(
            'GET',
            '/api/v2/shop/product-attributes/dishwasher_safe',
            [],
            [],
            self::CONTENT_TYPE_HEADER
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product/get_product_attribute_with_default_locale_translation',
            Response::HTTP_OK
        );
    }

    /** @test */
    public function it_returns_product_attribute_with_translations_in_locale_from_header(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'product/product_attribute.yaml']);

        $this->client->request(
            'GET',
            '/api/v2/shop/product-attributes/dishwasher_safe',
            [],
            [],
            array_merge(self::CONTENT_TYPE_HEADER, ['HTTP_ACCEPT_LANGUAGE' => 'pl_PL'])
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product/get_product_attribute_with_pl_PL_locale_translation',
            Response::HTTP_OK
        );
    }
}
