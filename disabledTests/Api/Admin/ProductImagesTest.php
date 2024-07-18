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

use Sylius\Bundle\ApiBundle\Serializer\ImageNormalizer;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
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
    public function it_gets_all_product_images_with_an_image_filter(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/product-images',
            parameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'sylius_small'],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/get_product_images_with_an_image_filter_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_prevents_getting_all_product_images_with_an_invalid_image_filter(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/product-images',
            parameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'invalid'],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'common/image/invalid_filter',
            Response::HTTP_BAD_REQUEST,
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
            'admin/product_image/get_product_images_for_product_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_product_images_for_the_given_product_with_an_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/products/%s/images', $product->getCode()),
            parameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'sylius_small'],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/get_product_images_for_product_with_an_image_filter_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_prevents_getting_product_images_for_the_given_product_with_an_invalid_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/products/%s/images', $product->getCode()),
            parameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'invalid'],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'common/image/invalid_filter',
            Response::HTTP_BAD_REQUEST,
        );
    }

    /** @test */
    public function it_filters_product_images_by_the_given_variant(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant_mug_blue'];

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/product-images',
            parameters: [
                'productVariants' => $productVariant->getCode(),
            ],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/get_product_images_filtered_by_variant_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_filters_product_images_by_the_given_variant_with_an_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant_mug_blue'];

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/product-images',
            parameters: [
                ImageNormalizer::FILTER_QUERY_PARAMETER => 'sylius_small',
                'productVariants' => $productVariant->getCode(),
            ],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/get_product_images_filtered_by_variant_with_an_image_filter_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_prevents_filtering_product_images_by_the_given_variant_with_an_invalid_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant_mug_blue'];

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/product-images',
            parameters: [
                ImageNormalizer::FILTER_QUERY_PARAMETER => 'invalid',
                'productVariants' => $productVariant->getCode(),
            ],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'common/image/invalid_filter',
            Response::HTTP_BAD_REQUEST,
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
    public function it_gets_a_product_image_with_an_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/product-images/%s', $productImage->getId()),
            parameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'sylius_small'],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/get_product_image_with_image_filter_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_prevents_getting_a_product_image_with_an_invalid_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/product-images/%s', $productImage->getId()),
            parameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'invalid'],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'common/image/invalid_filter',
            Response::HTTP_BAD_REQUEST,
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
    public function it_creates_a_product_image_with_type_and_variant(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant_mug_blue'];

        $this->client->request(
            method: 'POST',
            uri: sprintf('/api/v2/admin/products/%s/images', $product->getCode()),
            parameters: [
                'type' => 'banner',
                'productVariants' => [
                    sprintf('/api/v2/admin/product-variants/%s', $productVariant->getCode()),
                ],
            ],
            files: ['file' => $this->getUploadedFile('fixtures/mugs.jpg', 'mugs.jpg')],
            server: $header,
        );

        $response = $this->client->getResponse();
        $this->assertResponse(
            $response,
            'admin/product_image/post_product_image_with_type_and_variant_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_prevents_product_image_creation_with_unrelated_variant(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant_cap_yellow'];

        $this->client->request(
            method: 'POST',
            uri: sprintf('/api/v2/admin/products/%s/images', $product->getCode()),
            parameters: [
                'type' => 'banner',
                'productVariants' => [
                    sprintf('/api/v2/admin/product-variants/%s', $productVariant->getCode()),
                ],
            ],
            files: ['file' => $this->getUploadedFile('fixtures/mugs.jpg', 'mugs.jpg')],
            server: $header,
        );

        $response = $this->client->getResponse();
        $this->assertResponse(
            $response,
            'admin/product_image/post_product_image_with_invalid_variant_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_updates_only_the_type_and_variants_of_the_existing_product_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];

        /** @var ProductInterface $product */
        $product = $fixtures['product_cap'];

        /** @var ProductVariantInterface $productVariantBlue */
        $productVariantBlue = $fixtures['product_variant_mug_blue'];

        /** @var ProductVariantInterface $productVariantRed */
        $productVariantRed = $fixtures['product_variant_mug_red'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/product-images/%s', $productImage->getId()),
            server: $header,
            content: json_encode([
                'type' => 'logo',
                'owner' => sprintf('/api/v2/admin/products/%s', $product->getCode()),
                'path' => 'logo.jpg',
                'productVariants' => [
                    sprintf('/api/v2/admin/product-variants/%s', $productVariantBlue->getCode()),
                    sprintf('/api/v2/admin/product-variants/%s', $productVariantRed->getCode()),
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/put_product_image_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_prevents_product_image_update_with_unrelated_variant(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant_cap_yellow'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/product-images/%s', $productImage->getId()),
            server: $header,
            content: json_encode([
                'productVariants' => [
                    sprintf('/api/v2/admin/product-variants/%s', $productVariant->getCode()),
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/put_product_image_with_invalid_variant_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_deletes_a_product_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/product-images/%s', $productImage->getId()),
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }
}
