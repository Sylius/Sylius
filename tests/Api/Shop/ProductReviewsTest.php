<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
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
        $fixtures = $this->loadFixturesFromFiles(['product/product_variant.yaml']);
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
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product_review/create_product_review',
            Response::HTTP_CREATED,
        );
    }
}
