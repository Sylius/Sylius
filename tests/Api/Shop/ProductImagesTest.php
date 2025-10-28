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

use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\ImageNormalizer;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductImagesTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDefaultGetHeaders();
    }

    #[Test]
    public function it_gets_product_images(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['product/product_image.yaml']);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];

        $this->requestGet(
            uri: sprintf('/api/v2/shop/products/%s/images', $product->getCode()),
            headers: ['HTTPS' => true],
        );

        $this->assertResponse($this->client->getResponse(), 'shop/product_image/get_product_images');
    }

    #[Test]
    public function it_gets_product_images_with_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['product/product_image.yaml']);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];

        $this->requestGet(
            sprintf('/api/v2/shop/products/%s/images', $product->getCode()),
            [ImageNormalizer::FILTER_QUERY_PARAMETER => 'sylius_small'],
            ['HTTPS' => true],
        );

        $this->assertResponse($this->client->getResponse(), 'shop/product_image/get_product_images_with_image_filter');
    }

    #[Test]
    public function it_prevents_getting_product_images_with_an_invalid_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['product/product_image.yaml']);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];

        $this->requestGet(
            sprintf('/api/v2/shop/products/%s/images', $product->getCode()),
            [ImageNormalizer::FILTER_QUERY_PARAMETER => 'invalid'],
            ['HTTPS' => true],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'common/image/invalid_filter',
            Response::HTTP_BAD_REQUEST,
        );
    }

    #[Test]
    public function it_gets_a_product_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['product/product_image.yaml']);

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];
        /** @var ProductInterface $product */
        $product = $productImage->getOwner();

        $this->requestGet(
            uri: sprintf('/api/v2/shop/products/%s/images/%s', $product->getCode(), $productImage->getId()),
            headers: ['HTTPS' => true],
        );

        $this->assertResponse($this->client->getResponse(), 'shop/product_image/get_product_image_response');
    }

    #[Test]
    public function it_gets_a_product_image_with_an_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['product/product_image.yaml']);

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];
        /** @var ProductInterface $product */
        $product = $productImage->getOwner();

        $this->requestGet(
            sprintf('/api/v2/shop/products/%s/images/%s', $product->getCode(), $productImage->getId()),
            [ImageNormalizer::FILTER_QUERY_PARAMETER => 'sylius_small'],
            ['HTTPS' => true],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product_image/get_product_image_with_image_filter_response',
        );
    }

    #[Test]
    public function it_prevents_getting_a_product_image_with_an_invalid_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['product/product_image.yaml']);

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];
        /** @var ProductInterface $product */
        $product = $productImage->getOwner();

        $this->requestGet(
            sprintf('/api/v2/shop/products/%s/images/%s', $product->getCode(), $productImage->getId()),
            [ImageNormalizer::FILTER_QUERY_PARAMETER => 'invalid'],
            ['HTTPS' => true],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'common/image/invalid_filter',
            Response::HTTP_BAD_REQUEST,
        );
    }
}
