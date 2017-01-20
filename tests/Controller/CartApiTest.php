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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class CartApiTest extends JsonApiTestCase
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
        $this->client->request('GET', '/api/v1/carts/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_details_of_an_order_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/carts/-1', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_get_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $orderData = $this->loadFixturesFromFile('resources/carts.yml');

        $this->client->request('GET', '/api/v1/carts/'.$orderData['order_001']->getId(), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_creating_cart_for_non_authenticated_user()
    {
        $this->client->request('POST', '/api/v1/carts/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_cart_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/v1/carts/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_create_cart()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/carts.yml');

        $data =
<<<EOT
        {
            "customer": "oliver.queen@star-city.com",
            "channel": "WEB",
            "locale_code": "en_US"
        }
EOT;

        $this->client->request('POST', '/api/v1/carts/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/show_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_denies_getting_carts_for_non_authenticated_user()
    {
        $this->client->request('GET', '/api/v1/carts/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_to_get_carts_list()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/carts.yml');

        $this->client->request('GET', '/api/v1/carts/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_trying_to_delete_order_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/v1/carts/-1', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_delete_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $carts = $this->loadFixturesFromFile('resources/carts.yml');

        $this->client->request('DELETE', '/api/v1/carts/'.$carts['order_001']->getId(), [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/v1/carts/'.$carts['order_001']->getId(), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_denies_adding_a_product_to_cart_for_non_authenticated_user()
    {
        $this->client->request('POST', '/api/v1/carts/1/items/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_item_to_cart_without_providing_all_needed_fields()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $carts = $this->loadFixturesFromFile('resources/carts.yml');

        $this->client->request('POST', sprintf('/api/v1/carts/%s/items/', $carts['order_001']->getId()), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/add_to_cart_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_adds_an_item_to_the_cart()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $carts = $this->loadFixturesFromFile('resources/carts.yml');

        $data =
<<<EOT
        {
            "variant": "MUG_SW",
            "quantity": 1
        }
EOT;
        $this->client->request('POST', sprintf('/api/v1/carts/%s/items/', $carts['order_001']->getId()), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/add_to_cart_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_item_with_negative_quantity()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $carts = $this->loadFixturesFromFile('resources/carts.yml');

        $data =
<<<EOT
        {
            "variant": "MUG_SW",
            "quantity": -1
        }
EOT;
        $this->client->request('POST', sprintf('/api/v1/carts/%s/items/', $carts['order_001']->getId()), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/add_to_cart_quantity_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_adds_an_item_to_the_cart_with_quantity_bigger_than_one()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $carts = $this->loadFixturesFromFile('resources/carts.yml');

        $data =
<<<EOT
        {
            "variant": "MUG_SW",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/api/v1/carts/%s/items/', $carts['order_001']->getId()), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/add_to_cart_with_bigger_quantity_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_adds_an_item_to_the_cart_with_that_contains_tracked_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $carts = $this->loadFixturesFromFile('resources/carts.yml');

        $data =
<<<EOT
        {
            "variant": "HARD_AVAILABLE_MUG",
            "quantity": 1
        }
EOT;
        $this->client->request('POST', sprintf('/api/v1/carts/%s/items/', $carts['order_001']->getId()), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/add_to_cart_hard_available_item_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_checks_if_requested_variant_is_available()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $carts = $this->loadFixturesFromFile('resources/carts.yml');

        $data =
<<<EOT
        {
            "variant": "HARD_AVAILABLE_MUG",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', sprintf('/api/v1/carts/%s/items/', $carts['order_001']->getId()), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/add_to_cart_hard_available_item_validation_error_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_denies_updating_a_cart_item_quantity_for_non_authenticated_user()
    {
        $this->client->request('PUT', '/api/v1/carts/1/items/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_items_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $fulfilledCart = $this->loadFixturesFromFile('resources/fulfilled_cart.yml');
        /** @var OrderInterface $order */
        $order = $fulfilledCart['fulfilled_cart'];
        /** @var OrderItemInterface $orderItem */
        $orderItem = $fulfilledCart['sw_mug_item'];

        $data =
<<<EOT
        {
            "variant": "MUG_SW"
        }
EOT;

        $url = sprintf('/api/v1/carts/%s/items/%s', $order->getId(), $orderItem->getId());

        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/update_cart_item_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_updates_item_quantity()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $fulfilledCart = $this->loadFixturesFromFile('resources/fulfilled_cart.yml');
        /** @var OrderInterface $order */
        $order = $fulfilledCart['fulfilled_cart'];
        /** @var OrderItemInterface $orderItem */
        $orderItem = $fulfilledCart['sw_mug_item'];

        $url = sprintf('/api/v1/carts/%s/items/%s', $order->getId(), $orderItem->getId());

        $data =
<<<EOT
        {
            "quantity": 3
        }
EOT;
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_checks_if_requested_variant_is_available_during_quantity_update()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $fulfilledCart = $this->loadFixturesFromFile('resources/fulfilled_cart.yml');
        /** @var OrderInterface $order */
        $order = $fulfilledCart['fulfilled_cart'];
        /** @var OrderItemInterface $orderItem */
        $orderItem = $fulfilledCart['hard_available_mug_item'];

        $url = sprintf('/api/v1/carts/%s/items/%s', $order->getId(), $orderItem->getId());

        $data =
<<<EOT
        {
            "quantity": 3
        }
EOT;
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/update_hard_available_cart_item_validation_error_response', Response::HTTP_BAD_REQUEST);
    }
}
