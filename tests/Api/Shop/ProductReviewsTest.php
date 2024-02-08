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
            'GET',
            sprintf('/api/v2/shop/product-reviews/%s', $review->getId()),
            [],
            [],
            self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product/get_product_review',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_product_reviews(): void
    {
        $this->loadFixturesFromFile('product/product_review.yaml');;

        $this->client->request('GET', '/api/v2/shop/product-reviews', [], [], self::CONTENT_TYPE_HEADER);

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product/get_product_reviews',
            Response::HTTP_OK,
        );
    }
}
