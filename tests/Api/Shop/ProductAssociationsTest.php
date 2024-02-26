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

use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductAssociationsTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_product_association(): void
    {
        $fixtures = $this->loadFixturesFromFile('product/product_with_many_locales.yaml');

        /** @var ProductAssociationInterface $association */
        $association = $fixtures['product_association'];
        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/product-associations/%s', $association->getId()),
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product_association/get_product_association_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_returns_nothing_if_association_not_found(): void
    {
        $this->loadFixturesFromFile('product/product_with_many_locales.yaml');

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/product-associations/%s', 'wrong input'),
            server: self::CONTENT_TYPE_HEADER,
        );

        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
