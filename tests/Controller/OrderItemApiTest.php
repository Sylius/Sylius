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
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderItemApiTest extends JsonApiTestCase
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
    public function it_denies_order_item_creation_for_non_authenticated_user()
    {
        $this->client->request('POST', '/api/orders/1/items/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_order_item_for_unexisting_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/orders/1/items/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order_item/create_unexisting_order', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_order_item_without_specifying_unit_price()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/channels.yml');
        $orders = $this->loadFixturesFromFile('resources/orders.yml');

        $this->client->request('POST', sprintf('/api/orders/%d/items/', $orders['order1']->getId()), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order_item/create_invalid_unit_price', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_order_item_without_selecting_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/channels.yml');
        $orders = $this->loadFixturesFromFile('resources/orders.yml');

        $data =
<<<EOT
        {
            "unitPrice": 1000
        }
EOT;

        $this->client->request('POST', sprintf('/api/orders/%d/items/', $orders['order1']->getId()), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order_item/create_invalid_variant', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    function it_allows_to_create_order_item_for_selected_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/channels.yml');
        $orders = $this->loadFixturesFromFile('resources/orders.yml');
        $products = $this->loadFixturesFromFile('resources/product.yml');

        $data = json_encode([
            'variant' => $products['productVariant1']->getId(),
            'quantity' => 1,
            'unitPrice' => $products['productVariant1']->getPrice(),
        ]);

        $this->client->request('POST', sprintf('/api/orders/%d/items/', $orders['order1']->getId()), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order_item/create_response', Response::HTTP_CREATED);
    }
}
