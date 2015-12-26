<?php

namespace Sylius\Bundle\CoreBundle\Tests\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ShippingCategoryApiTest extends JsonApiTestCase
{
    public function testCreateShippingCategoryAccessDeniedResponse()
    {
        $this->client->request('POST', '/api/shipping-categories/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    public function testCreateShippingCategoryValidationFailResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/shipping-categories/', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'CONTENT_TYPE' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    public function testCreateShippingCategoryResponse()
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
        $this->assertResponse($response, 'shipping_category/new_shipping_category_response', Response::HTTP_CREATED);
    }

    public function testGetShippingCategoriesListAccessDeniedResponse()
    {
        $this->client->request('GET', '/api/shipping-categories/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    public function testGetShippingCategoriesListResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/shipping_categories.yml');

        $this->client->request('GET', '/api/shipping-categories/', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/get_shipping_categories_response', Response::HTTP_OK);
    }

    public function testGetShippingCategoryAccessDeniedResponse()
    {
        $this->client->request('GET', '/api/shipping-categories/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    public function testGetShippingCategoryWhichDoesNotExistResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/shipping-categories/-1', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    public function testGetShippingCategoryResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $shippingCategories = $this->loadFixturesFromFile('resources/shipping_categories.yml');

        $this->client->request('GET', '/api/shipping-categories/'.$shippingCategories['shipping_category_1']->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/shipping_category_response', Response::HTTP_OK);
    }

    public function testFullUpdateShippingCategoryAccessDeniedResponse()
    {
        $this->client->request('PUT', '/api/shipping-categories/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    public function testFullUpdateShippingCategoryWhichDoesNotExistResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/shipping-categories/-1', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    public function testFullUpdateShippingCategoryValidationFailResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $shippingCategories = $this->loadFixturesFromFile('resources/shipping_categories.yml');

        $this->client->request('PUT', '/api/shipping-categories/'.$shippingCategories['shipping_category_1']->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'CONTENT_TYPE' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    public function testFullUpdateShippingCategoryResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $shippingCategories = $this->loadFixturesFromFile('resources/shipping_categories.yml');

        $this->client->request('PUT', '/api/shipping-categories/'.$shippingCategories['shipping_category_1']->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'CONTENT_TYPE' => 'application/json',
        ], '{"name": "Light", "description": "Light weight items"}');

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/shipping-categories/'.$shippingCategories['shipping_category_1']->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/updated_shipping_category_response', Response::HTTP_OK);
    }

    public function testPartialUpdateShippingCategoryWhichDoesNotExistResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PATCH', '/api/shipping-categories/-1', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    public function testPartialUpdateShippingCategoryResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $shippingCategories = $this->loadFixturesFromFile('resources/shipping_categories.yml');

        $this->client->request('PATCH', '/api/shipping-categories/'.$shippingCategories['shipping_category_1']->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'CONTENT_TYPE' => 'application/json',
        ], '{"name": "Light", "description": "Light weight items"}');

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/shipping-categories/'.$shippingCategories['shipping_category_1']->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/updated_shipping_category_response', Response::HTTP_OK);
    }
}
