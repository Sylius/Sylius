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
    public function it_gets_product_review(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_review.yaml']);
        $header = $this->getLoggedHeader();

        /** @var ReviewInterface $review */
        $review = $fixtures['customer_review'];

        $this->client->request(
            'GET',
            sprintf('/api/v2/admin/product-reviews/%s', $review->getId()),
            [],
            [],
            $header,
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
        $header = $this->getLoggedHeader();

        $this->client->request('GET', '/api/v2/admin/product-reviews', [], [], $header);

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
        $header = $this->getLoggedHeader();

        /** @var ProductReviewerInterface $review */
        $review = $fixtures['new_review'];

        $this->client->request(
            'PATCH',
            sprintf('/api/v2/admin/product-reviews/%s/accept', $review->getId()),
            [],
            [],
            $header,
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
        $header = $this->getLoggedHeader();

        /** @var ProductReviewerInterface $review */
        $review = $fixtures['new_review'];

        $this->client->request(
            'PATCH',
            sprintf('/api/v2/admin/product-reviews/%s/reject', $review->getId()),
            [],
            [],
            $header,
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
        $header = $this->getLoggedHeader();

        /** @var ProductReviewerInterface $review */
        $review = $fixtures['customer_review'];

        $this->client->request(
            'PUT',
            sprintf('/api/v2/admin/product-reviews/%s', $review->getId()),
            [],
            [],
            $header,
            json_encode([
                'title' => 'Bestest product!',
                'comment' => 'I\'ve never bought anything better.',
                'rating' => 5,
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_review/put_product_review',
            Response::HTTP_OK,
        );
    }

    private function getLoggedHeader(): array
    {
        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;

        return array_merge($header, self::CONTENT_TYPE_HEADER);
    }
}
