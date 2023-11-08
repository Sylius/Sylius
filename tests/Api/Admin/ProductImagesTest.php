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

namespace Sylius\Tests\Api\Admin;

use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ProductImagesTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_denies_access_to_a_product_images_list_for_not_authenticated_user(): void
    {
        $this->loadFixturesFromFile('product/product_image.yaml');

        $this->client->request(method: 'GET', uri: '/api/v2/admin/product-images');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function it_gets_all_product_images(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/product-images', server: $header);

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/get_product_images_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_a_product_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/product-images/%s', $productImage->getId()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/get_product_image_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_product_images_for_the_given_product(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/products/%s/images', $product->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/get_product_images_for_given_product_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_a_product_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];

        $this->client->request(
            method: 'POST',
            uri: sprintf('/api/v2/admin/products/%s/images', $product->getCode()),
            files: ['file' => $this->getUploadedFile('fixtures/mugs.jpg', 'mugs.jpg')],
            server: $header,
        );

        $response = $this->client->getResponse();
        $this->assertResponse(
            $response,
            'admin/product_image/post_product_image_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_creates_a_product_image_with_type(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];

        $this->client->request(
            method: 'POST',
            uri: sprintf('/api/v2/admin/products/%s/images', $product->getCode()),
            parameters: ['type' => 'banner'],
            files: ['file' => $this->getUploadedFile('fixtures/mugs.jpg', 'mugs.jpg')],
            server: $header,
        );

        $response = $this->client->getResponse();
        $this->assertResponse(
            $response,
            'admin/product_image/post_product_image_with_type_response',
            Response::HTTP_CREATED,
        );
    }
}
