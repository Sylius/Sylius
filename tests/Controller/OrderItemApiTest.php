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
 * @author Daniel Gorgan <danut007ro@gmail.com>
 */
final class OrderItemApiTest extends JsonApiTestCase
{
    /** @var array */
    private static $authorizedHeaderWithContentType = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'CONTENT_TYPE' => 'application/json',
    ];

    /**
     * @test
     */
    public function it_denies_access_for_not_authenticated_users()
    {
        $this->client->request('GET', '/api/orders/1/items');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

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
    public function it_does_not_allow_to_create_order_item_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $orderItemsData = $this->loadFixturesFromFile('resources/order_items.yml');

        $this->client->request('POST', "/api/orders/{$orderItemsData['order']->getId()}/items/", [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order_item/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_order_item_without_selecting_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $orderItemsData = $this->loadFixturesFromFile('resources/order_items.yml');

        $data =
<<<EOT
        {
            "quantity": 1,
            "unitPrice": 1000
        }
EOT;

        $this->client->request('POST', "/api/orders/{$orderItemsData['order']->getId()}/items/", [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order_item/create_invalid_variant', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_create_order_item_for_selected_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $orderItemsData = $this->loadFixturesFromFile('resources/order_items.yml');

        $data =
<<<EOT
        {
            "quantity": 2,
            "unitPrice": 100,
            "variant": {$orderItemsData['productVariant1']->getId()}
        }
EOT;

        $this->client->request('POST', "/api/orders/{$orderItemsData['order']->getId()}/items/", [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order_item/create_response', Response::HTTP_CREATED);
    }
}
