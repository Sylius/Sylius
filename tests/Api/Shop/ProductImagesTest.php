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

use Sylius\Bundle\ApiBundle\Serializer\ImageNormalizer;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductImagesTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDefaultGetHeaders();
    }

    /** @test */
    public function it_gets_a_product_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'product/product_image.yaml',
        ]);

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];

        $this->requestGet(
            uri: sprintf('/api/v2/shop/product-images/%s', $productImage->getId()),
            headers: ['HTTPS' => true]
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product_image/get_product_image_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_a_product_image_with_an_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'product/product_image.yaml',
        ]);

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];

        $this->requestGet(
            uri: sprintf('/api/v2/shop/product-images/%s', $productImage->getId()),
            queryParameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'sylius_small'],
            headers: ['HTTPS' => true]
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product_image/get_product_image_with_image_filter_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_prevents_getting_a_product_image_with_an_invalid_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'product/product_image.yaml',
        ]);

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];

        $this->requestGet(
            uri: sprintf('/api/v2/shop/product-images/%s', $productImage->getId()),
            queryParameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'invalid'],
            headers: ['HTTPS' => true]
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'common/image/invalid_filter',
            Response::HTTP_BAD_REQUEST,
        );
    }
}
