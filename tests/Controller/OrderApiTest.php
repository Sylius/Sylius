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
    public function it_denies_getting_an_order_for_non_authenticated_user()
    {
        $this->client->request('GET', '/api/v1/orders/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_details_of_an_order_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/orders/-1', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_get_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $orderData = $this->loadFixturesFromFile('resources/orders.yml');

        $this->client->request('GET', '/api/v1/orders/'.$orderData['order-001']->getId(), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_creating_order_for_non_authenticated_user()
    {
        $this->client->request('POST', '/api/v1/orders/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_order_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/v1/orders/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_create_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/orders.yml');

        $data =
<<<EOT
        {
            "customer": "oliver.queen@star-city.com",
            "channel": "WEB",
            "locale_code": "en_US"
        }
EOT;

        $this->client->request('POST', '/api/v1/orders/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'order/show_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_denies_getting_orders_for_non_authenticated_user()
    {
        $this->client->request('GET', '/api/v1/orders/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_to_get_orders_list()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/orders.yml');

        $this->client->request('GET', '/api/v1/orders/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_trying_to_delete_order_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/v1/orders/-1', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_delete_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $orders = $this->loadFixturesFromFile('resources/orders.yml');

        $this->client->request('DELETE', '/api/v1/orders/'.$orders['order-001']->getId(), [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/v1/orders/'.$orders['order-001']->getId(), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }
}
