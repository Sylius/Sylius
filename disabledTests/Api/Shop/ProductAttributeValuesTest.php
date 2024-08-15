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

final class ProductAttributeValuesTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_attribute_value(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['channel/channel.yaml', 'product/product_attribute.yaml']);
        $attributeValue = $fixtures['product_attribute_value_checkbox'];

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/product-attribute-values/' . $attributeValue->getId(),
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product_attribute/get_product_attribute_value',
            Response::HTTP_OK,
        );
    }
}
