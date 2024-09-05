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

use Sylius\Component\Core\Model\ProductReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ProductReviewsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_product_review(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_review.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ReviewInterface $review */
        $review = $fixtures['customer_review'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/product-reviews/%s', $review->getId()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_review/get_product_review',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_product_reviews(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_review.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/product-reviews', server: $header);

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_review/get_product_reviews',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_accepts_a_product_review(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_review.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::PATCH_CONTENT_TYPE_HEADER);

        /** @var ProductReviewerInterface $review */
        $review = $fixtures['new_review'];

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/admin/product-reviews/%s/accept', $review->getId()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_review/accept_product_review',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_rejects_a_product_review(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_review.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::PATCH_CONTENT_TYPE_HEADER);

        /** @var ProductReviewerInterface $review */
        $review = $fixtures['new_review'];

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/admin/product-reviews/%s/reject', $review->getId()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_review/reject_product_review',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_updates_a_product_review(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_review.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductReviewerInterface $review */
        $review = $fixtures['customer_review'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/product-reviews/%s', $review->getId()),
            server: $header,
            content: json_encode([
                'title' => 'Bestest product!',
                'comment' => 'I\'ve never bought anything better.',
                'rating' => 5,
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_review/put_product_review',
            Response::HTTP_OK,
        );
    }

    /**
     * @test
     *
     * @dataProvider invalidRatingRangeDataProvider
     */
    public function it_does_not_allow_to_update_a_product_review_with_invalid_rating(int $rating): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_review.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductReviewerInterface $review */
        $review = $fixtures['customer_review'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/product-reviews/%s', $review->getId()),
            server: $header,
            content: json_encode([
                'rating' => $rating,
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                [
                    'propertyPath' => 'rating',
                    'message' => 'Review rating must be between 1 and 5.',
                ],
            ],
        );
    }

    public function invalidRatingRangeDataProvider(): iterable
    {
        yield [-1];
        yield [6];
    }
}
