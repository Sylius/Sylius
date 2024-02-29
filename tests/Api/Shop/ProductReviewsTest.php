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

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductReviewsTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_product_review(): void
    {
        $fixtures = $this->loadFixturesFromFile('product/product_review.yaml');
        /** @var ReviewInterface $review */
        $review = $fixtures['customer_review'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/product-reviews/%s', $review->getId()),
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product_review/get_product_review',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_product_reviews(): void
    {
        $this->loadFixturesFromFile('product/product_review.yaml');

        $this->client->request(method: 'GET', uri: '/api/v2/shop/product-reviews', server: self::CONTENT_TYPE_HEADER);

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product_review/get_product_reviews',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_a_product_review(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);
        /** @var ProductInterface $product */
        $product = $fixtures['product'];

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/product-reviews',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'title' => 'Greatest product!',
                'rating' => 3,
                'comment' => 'I\'ve never bought anything better.',
                'email' => 'test@test.com',
                'product' => '/api/v2/shop/products/' . $product->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product_review/create_product_review',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_prevents_from_creating_a_product_review_with_non_existing_product(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/product-reviews',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'title' => 'Greatest product!',
                'rating' => 3,
                'comment' => 'I\'ve never bought anything better.',
                'email' => 'test@test.com',
                'product' => '/api/v2/shop/products/NON-EXISTING-PRODUCT',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function it_prevents_from_creating_a_product_review_without_email(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);
        /** @var ProductInterface $product */
        $product = $fixtures['product'];

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/product-reviews',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'title' => 'Greatest product!',
                'rating' => 3,
                'comment' => 'I\'ve never bought anything better.',
                'product' => '/api/v2/shop/products/' . $product->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                [
                    'propertyPath' => 'email',
                    'message' => 'Please enter your email.',
                ],
            ],
        );
    }

    /** @test */
    public function it_prevents_from_creating_a_product_review_if_required_fields_are_missing(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'product/product_variant.yaml',
        ]);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/product-reviews',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'email' => 'test@test.com',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product_review/create_product_review_with_missing_fields',
            Response::HTTP_BAD_REQUEST,
        );
    }
}
