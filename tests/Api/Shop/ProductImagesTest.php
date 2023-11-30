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

use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductImagesTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_one_product_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['product/product_image.yaml', 'authentication/api_administrator.yaml']);
        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/product-images/%s', (string) $productImage->getId()),
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/product/get_product_image_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_one_filtered_product_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['product/product_image.yaml', 'authentication/api_administrator.yaml']);
        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/product-images/%s', (string) $productImage->getId()),
            parameters: ['filter' => 'sylius_small'],
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/product/get_filtered_product_image_response', Response::HTTP_OK);
    }
}
