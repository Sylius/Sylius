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
use Symfony\Component\HttpFoundation\Response;

final class ProductImagesTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        $this->setUpAdminContext();
        $this->setUpDefaultGetHeaders();
        $this->setUpDefaultPostHeaders();
        $this->setUpDefaultPutHeaders();

        parent::setUp();
    }

    /** @test */
    public function it_gets_product_images_for_the_given_product(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];

        $this->requestGet(sprintf('/api/v2/admin/products/%s/images', $product->getCode()));

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/get_product_images_for_product_response',
        );
    }

    /** @test */
    public function it_denies_access_to_a_product_images_list_for_not_authenticated_user(): void
    {
        $this->disableAdminContext();

        $fixtures = $this->loadFixturesFromFile('product/product_image.yaml');

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];

        $this->requestGet(sprintf('/api/v2/admin/products/%s/images', $product->getCode()));

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function it_gets_product_images_for_the_given_product_with_an_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];

        $this->requestGet(
            sprintf('/api/v2/admin/products/%s/images', $product->getCode()),
            [ImageNormalizer::FILTER_QUERY_PARAMETER => 'sylius_small'],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/get_product_images_for_product_with_an_image_filter_response',
        );
    }

    /** @test */
    public function it_prevents_getting_product_images_for_the_given_product_with_an_invalid_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];

        $this->requestGet(
            sprintf('/api/v2/admin/products/%s/images', $product->getCode()),
            [ImageNormalizer::FILTER_QUERY_PARAMETER => 'invalid'],
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

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant_mug_blue'];

        $this->requestGet(
            sprintf('/api/v2/admin/products/%s/images', $productVariant->getProduct()->getCode()),
            ['productVariants' => sprintf('/api/v2/admin/product-variants/%s', $productVariant->getCode())],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/get_product_images_filtered_by_variant_response',
        );
    }

    /** @test */
    public function it_filters_product_images_by_the_given_variant_code(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant_mug_blue'];

        $this->requestGet(
            sprintf('/api/v2/admin/products/%s/images', $productVariant->getProduct()->getCode()),
            ['productVariants.code' => $productVariant->getCode()],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/get_product_images_filtered_by_variant_code_response',
        );
    }

    /** @test */
    public function it_filters_product_images_by_the_given_variant_code_with_an_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant_mug_blue'];

        $this->requestGet(
            sprintf('/api/v2/admin/products/%s/images', $productVariant->getProduct()->getCode()),
            [
                ImageNormalizer::FILTER_QUERY_PARAMETER => 'sylius_small',
                'productVariants.code' => $productVariant->getCode(),
            ],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/get_product_images_filtered_by_variant_code_with_an_image_filter_response',
        );
    }

    /** @test */
    public function it_prevents_filtering_product_images_by_the_given_variant_code_with_an_invalid_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant_mug_blue'];

        $this->requestGet(
            sprintf('/api/v2/admin/products/%s/images', $productVariant->getProduct()->getCode()),
            [
                ImageNormalizer::FILTER_QUERY_PARAMETER => 'invalid',
                'productVariants.code' => $productVariant->getCode(),
            ],
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

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];
        /** @var ProductInterface $product */
        $product = $productImage->getOwner();

        $this->requestGet(
            sprintf('/api/v2/admin/products/%s/images/%s', $product->getCode(), $productImage->getId()),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/get_product_image_response',
        );
    }

    /** @test */
    public function it_gets_a_product_image_with_an_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];
        /** @var ProductInterface $product */
        $product = $productImage->getOwner();

        $this->requestGet(
            sprintf('/api/v2/admin/products/%s/images/%s', $product->getCode(), $productImage->getId()),
            [ImageNormalizer::FILTER_QUERY_PARAMETER => 'sylius_small'],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/get_product_image_with_image_filter_response',
        );
    }

    /** @test */
    public function it_prevents_getting_a_product_image_with_an_invalid_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];
        /** @var ProductInterface $product */
        $product = $productImage->getOwner();

        $this->requestGet(
            sprintf('/api/v2/admin/products/%s/images/%s', $product->getCode(), $productImage->getId()),
            [ImageNormalizer::FILTER_QUERY_PARAMETER => 'invalid'],
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

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];

        $this->requestPost(
            sprintf('/api/v2/admin/products/%s/images', $product->getCode()),
            headers: $this->headerBuilder()->withMultipartFormDataContentType()->build(),
            files: ['file' => $this->getUploadedFile('fixtures/mugs.jpg', 'mugs.jpg')],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/post_product_image_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_creates_a_product_image_with_type_and_variant(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant_mug_blue'];

        $this->requestPost(
            sprintf('/api/v2/admin/products/%s/images', $product->getCode()),
            parameters: [
                'type' => 'banner',
                'productVariants' => [
                    sprintf('/api/v2/admin/product-variants/%s', $productVariant->getCode()),
                ],
            ],
            headers: $this->headerBuilder()->withMultipartFormDataContentType()->build(),
            files: ['file' => $this->getUploadedFile('fixtures/mugs.jpg', 'mugs.jpg')],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/post_product_image_with_type_and_variant_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_prevents_product_image_creation_with_unrelated_variant(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant_cap_yellow'];

        $this->requestPost(
            sprintf('/api/v2/admin/products/%s/images', $product->getCode()),
            parameters: [
                'type' => 'banner',
                'productVariants' => [
                    sprintf('/api/v2/admin/product-variants/%s', $productVariant->getCode()),
                ],
            ],
            headers: $this->headerBuilder()->withMultipartFormDataContentType()->build(),
            files: ['file' => $this->getUploadedFile('fixtures/mugs.jpg', 'mugs.jpg')],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/post_product_image_with_invalid_variant_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_updates_only_the_type_and_variants_of_the_existing_product_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];
        /** @var ProductInterface $product */
        $product = $productImage->getOwner();
        /** @var ProductInterface $product */
        $productCap = $fixtures['product_cap'];
        /** @var ProductVariantInterface $productVariantBlue */
        $productVariantBlue = $fixtures['product_variant_mug_blue'];
        /** @var ProductVariantInterface $productVariantRed */
        $productVariantRed = $fixtures['product_variant_mug_red'];

        $this->requestPut(
            sprintf('/api/v2/admin/products/%s/images/%s', $product->getCode(), $productImage->getId()),
            body: [
                'type' => 'logo',
                'owner' => sprintf('/api/v2/admin/products/%s', $productCap->getCode()),
                'path' => 'logo.jpg',
                'productVariants' => [
                    sprintf('/api/v2/admin/product-variants/%s', $productVariantBlue->getCode()),
                    sprintf('/api/v2/admin/product-variants/%s', $productVariantRed->getCode()),
                ],
            ],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_image/put_product_image_response',
        );
    }

    /** @test */
    public function it_prevents_product_image_update_with_unrelated_variant(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_image.yaml']);

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];
        /** @var ProductInterface $product */
        $product = $productImage->getOwner();
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant_cap_yellow'];

        $this->requestPut(
            sprintf('/api/v2/admin/products/%s/images/%s', $product->getCode(), $productImage->getId()),
            body: [
                'productVariants' => [
                    sprintf('/api/v2/admin/product-variants/%s', $productVariant->getCode()),
                ],
            ],
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

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_mug_thumbnail'];
        /** @var ProductInterface $product */
        $product = $productImage->getOwner();

        $this->requestDelete(
            sprintf('/api/v2/admin/products/%s/images/%s', $product->getCode(), $productImage->getId()),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }
}
