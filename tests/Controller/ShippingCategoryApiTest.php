<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Tests\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ShippingCategoryApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function test_create_shipping_category_access_denied_response()
    {
        $this->client->request('POST', '/api/shipping-categories/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function test_create_shipping_category_validation_fail_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/shipping-categories/', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'CONTENT_TYPE' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function test_create_shipping_category_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "code": "reg",
            "name": "Regular",
            "description": "Regular weight items"
        }
EOT;

        $this->client->request('POST', '/api/shipping-categories/', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'CONTENT_TYPE' => 'application/json',
        ], $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function test_get_shipping_categories_list_access_denied_response()
    {
        $this->client->request('GET', '/api/shipping-categories/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function test_get_shipping_categories_list_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/shipping_categories.yml');

        $this->client->request('GET', '/api/shipping-categories/', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function test_get_shipping_category_access_denied_response()
    {
        $this->client->request('GET', '/api/shipping-categories/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function test_get_shipping_category_which_does_not_exist_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/shipping-categories/-1', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function test_get_shipping_category_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $shippingCategories = $this->loadFixturesFromFile('resources/shipping_categories.yml');

        $this->client->request('GET', '/api/shipping-categories/'.$shippingCategories['shipping_category_1']->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function test_full_update_shipping_category_access_denied_response()
    {
        $this->client->request('PUT', '/api/shipping-categories/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function test_full_update_shipping_category_which_does_not_exist_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/shipping-categories/-1', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function test_full_update_shipping_category_validation_fail_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $shippingCategories = $this->loadFixturesFromFile('resources/shipping_categories.yml');

        $this->client->request('PUT', '/api/shipping-categories/'.$shippingCategories['shipping_category_1']->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'CONTENT_TYPE' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/update_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function test_full_update_shipping_category_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $shippingCategories = $this->loadFixturesFromFile('resources/shipping_categories.yml');

        $data =
<<<EOT
        {
            "name": "Light",
            "description": "Light weight items"
        }
EOT;

        $this->client->request('PUT', '/api/shipping-categories/'.$shippingCategories['shipping_category_1']->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'CONTENT_TYPE' => 'application/json',
        ], $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/shipping-categories/'.$shippingCategories['shipping_category_1']->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/update_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function test_partial_update_shipping_category_which_does_not_exist_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PATCH', '/api/shipping-categories/-1', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function test_partial_update_shipping_category_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $shippingCategories = $this->loadFixturesFromFile('resources/shipping_categories.yml');

        $data =
<<<EOT
        {
            "name": "Light",
            "description": "Light weight items"
        }
EOT;

        $this->client->request('PATCH', '/api/shipping-categories/'.$shippingCategories['shipping_category_1']->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'CONTENT_TYPE' => 'application/json',
        ], $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/shipping-categories/'.$shippingCategories['shipping_category_1']->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/update_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function test_delete_shipping_category_which_does_not_exist_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/shipping-categories/-1', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function test_delete_shipping_category_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $shippingCategories = $this->loadFixturesFromFile('resources/shipping_categories.yml');

        $this->client->request('DELETE', '/api/shipping-categories/'.$shippingCategories['shipping_category_1']->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'CONTENT_TYPE' => 'application/json',
        ], []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/shipping-categories/'.$shippingCategories['shipping_category_1']->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }
}
