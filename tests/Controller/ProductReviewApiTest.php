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

namespace Sylius\Tests\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\HttpFoundation\Response;

final class ProductReviewApiTest extends JsonApiTestCase
{
    /**
     * @var array
     */
    private static $authorizedHeaderWithContentType = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'CONTENT_TYPE' => 'application/json',
    ];

    /**
     * @var array
     */
    private static $authorizedHeaderWithAccept = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'ACCEPT' => 'application/json',
    ];

    /**
     * @test
     */
    public function it_does_not_allows_showing_product_review_list_when_access_is_denied()
    {
        $productReviewsData = $this->loadFixturesFromFile('resources/product_reviews.yml');

        /** @var ProductInterface $product */
        $product = $productReviewsData['product1'];

        $this->client->request('GET', $this->getReviewListUrl($product));
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allows_showing_product_review_when_it_does_not_exist()
    {
        $productReviewsData = $this->loadFixturesFromFile('resources/product_reviews.yml');
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        /** @var ProductInterface $product */
        $product = $productReviewsData['product1'];

        $this->client->request('GET', $this->getReviewListUrl($product) . '0', [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_showing_product_review()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productReviewsData = $this->loadFixturesFromFile('resources/product_reviews.yml');

        /** @var ProductInterface $product */
        $product = $productReviewsData['product1'];

        /** @var ReviewInterface $productReview */
        $productReview = $productReviewsData['productReview1'];

        $this->client->request('GET', $this->getReviewUrl($product, $productReview), [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_review/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_indexing_product_reviews()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productReviewsData = $this->loadFixturesFromFile('resources/product_reviews.yml');

        /** @var ProductInterface $product */
        $product = $productReviewsData['product1'];

        $this->client->request('GET', $this->getReviewListUrl($product), [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_review/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_creating_product_review()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productReviewsData = $this->loadFixturesFromFile('resources/product_reviews.yml');

        /** @var ProductInterface $product */
        $product = $productReviewsData['product1'];

        $data =
<<<EOT
        {
          "title": "A good product",
          "rating": "3",
          "comment": "This is a good product.",
          "author": {
            "email": "my_review@example.com"
          }
        }
EOT;

        $this->client->request('POST', $this->getReviewListUrl($product), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_review/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_does_not_allows_creating_product_review_without_required_fields()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productReviewsData = $this->loadFixturesFromFile('resources/product_reviews.yml');

        /** @var ProductInterface $product */
        $product = $productReviewsData['product1'];

        $this->client->request('POST', $this->getReviewListUrl($product), [], [], static::$authorizedHeaderWithContentType, []);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_review/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allows_deleting_product_review_if_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productReviewsData = $this->loadFixturesFromFile('resources/product_reviews.yml');

        /** @var ProductInterface $product */
        $product = $productReviewsData['product1'];

        $this->client->request('DELETE', $this->getReviewListUrl($product) . '0', [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_deleting_product_review()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productReviewsData = $this->loadFixturesFromFile('resources/product_reviews.yml');

        /** @var ProductInterface $product */
        $product = $productReviewsData['product1'];

        /** @var ReviewInterface $productReview */
        $productReview = $productReviewsData['productReview1'];

        $this->client->request('DELETE', $this->getReviewUrl($product, $productReview), [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        /** @var ProductInterface $product */
        $product = $productReviewsData['product1'];

        $this->client->request('GET', $this->getReviewUrl($product, $productReview), [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_updating_information_about_product_review()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productReviewsData = $this->loadFixturesFromFile('resources/product_reviews.yml');

        /** @var ProductInterface $product */
        $product = $productReviewsData['product1'];

        /** @var ReviewInterface $productReview */
        $productReview = $productReviewsData['productReview1'];

        $data =
            <<<EOT
        {
            "title": "NEW_REVIEW_TITLE",
            "rating": "1",
            "comment": "NEW_REVIEW_COMMENT"
        }
EOT;
        $this->client->request('PUT', $this->getReviewUrl($product, $productReview), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_updating_partial_information_about_product_review()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productReviewsData = $this->loadFixturesFromFile('resources/product_reviews.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        /** @var ProductInterface $product */
        $product = $productReviewsData['product1'];

        /** @var ReviewInterface $productReview */
        $productReview = $productReviewsData['productReview1'];

        $data =
            <<<EOT
        {
            "comment": "A_NEW_REVIEW_COMMENT"
        }
EOT;

        $this->client->request('PATCH', $this->getReviewUrl($product, $productReview), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_accepting_product_review()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productReviewsData = $this->loadFixturesFromFile('resources/product_reviews.yml');

        /** @var ProductInterface $product */
        $product = $productReviewsData['product1'];

        /** @var ReviewInterface $productReview */
        $productReview = $productReviewsData['productReview1'];

        $this->client->request('PATCH', $this->getReviewUrl($product, $productReview) . '/accept', [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_review/accept_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allows_accepting_product_review_if_it_has_not_new_status()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productReviewsData = $this->loadFixturesFromFile('resources/product_reviews.yml');

        /** @var ProductInterface $product */
        $product = $productReviewsData['product1'];

        /** @var ReviewInterface $productReview */
        $productReview = $productReviewsData['productReview3'];

        $this->client->request('POST', $this->getReviewUrl($product, $productReview) . '/accept', [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_review/change_status_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_rejecting_product_review()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productReviewsData = $this->loadFixturesFromFile('resources/product_reviews.yml');

        /** @var ProductInterface $product */
        $product = $productReviewsData['product1'];

        /** @var ReviewInterface $productReview */
        $productReview = $productReviewsData['productReview1'];

        $this->client->request('PATCH', $this->getReviewUrl($product, $productReview) . '/reject', [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_review/reject_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allows_rejecting_product_review_if_it_has_not_new_status()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productReviewsData = $this->loadFixturesFromFile('resources/product_reviews.yml');

        /** @var ProductInterface $product */
        $product = $productReviewsData['product1'];

        /** @var ReviewInterface $productReview */
        $productReview = $productReviewsData['productReview3'];

        $this->client->request('POST', $this->getReviewUrl($product, $productReview) . '/accept', [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_review/change_status_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param ProductInterface $product
     *
     * @return string
     */
    private function getReviewListUrl(ProductInterface $product): string
    {
        return sprintf('/api/v1/products/%s/reviews/', $product->getCode());
    }

    /**
     * @param ProductInterface $product
     * @param ReviewInterface  $productReview
     *
     * @return string
     */
    private function getReviewUrl(ProductInterface $product, ReviewInterface $productReview): string
    {
        return sprintf('%s%s', $this->getReviewListUrl($product), $productReview->getId());
    }
}
