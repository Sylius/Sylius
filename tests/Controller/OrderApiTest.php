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
final class OrderApiTest extends JsonApiTestCase
{
    /**
     * @var array
     */
    private static $authorizedHeaderWithContentType = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'CONTENT_TYPE' => 'application/json',
    ];

    /**
     * @test
     */
    public function test_get_order_which_does_not_exist_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/orders/-1', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function test_get_order_access_denied_response()
    {
        $this->client->request('GET', '/api/orders/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function test_get_order_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $orderData = $this->loadFixturesFromFile('resources/orders.yml');

        $this->client->request('GET', '/api/orders/'.$orderData['order-001']->getId(), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function test_create_order_access_denied_response()
    {
        $this->client->request('POST', '/api/orders/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function test_create_order_validation_fail_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/orders/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function test_create_order_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/orders.yml');

        $data =
<<<EOT
        {
            "customer": "oliver.queen@star-city.com",
            "channel": "WEB",
            "localeCode": "en_US"
        }
EOT;

        $this->client->request('POST', '/api/orders/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'order/show_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function test_get_orders_list_access_denied_response()
    {
        $this->client->request('GET', '/api/orders/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function test_get_orders_list_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/orders.yml');

        $this->client->request('GET', '/api/orders/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function test_delete_order_which_does_not_exist_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/orders/-1', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function test_delete_order_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $orders = $this->loadFixturesFromFile('resources/orders.yml');

        $this->client->request('DELETE', '/api/orders/'.$orders['order-001']->getId(), [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/orders/'.$orders['order-001']->getId(), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }
}
