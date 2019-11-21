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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Symfony\Component\HttpFoundation\Response;

final class CartApiTest extends JsonApiTestCase
{
    /** @var array */
    private static $authorizedHeaderWithContentType = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'CONTENT_TYPE' => 'application/json',
    ];

    /** @var array */
    private static $authorizedHeaderWithAccept = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'ACCEPT' => 'application/json',
    ];

    /**
     * @test
     */
    public function it_denies_getting_an_cart_for_non_authenticated_user()
    {
        $this->client->request('GET', $this->getCartApiUrl('-1'));

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_details_of_an_cart_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', $this->getCartApiUrl('-1'), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_get_cart()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $cartData = $this->loadFixturesFromFile('resources/cart.yml');

        /** @var OrderInterface $cart */
        $cart = $cartData['order_001'];

        $this->client->request('GET', $this->getCartApiUrl((string) $cart->getId()), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_show_orders_in_state_other_than_cart()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $orderData = $this->loadFixturesFromFile('resources/order.yml');

        /** @var OrderInterface $cart */
        $cart = $orderData['order_001'];

        $this->client->request('GET', $this->getCartApiUrl((string) $cart->getId()), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_denies_creating_cart_for_non_authenticated_user()
    {
        $this->client->request('POST', $this->getCartApiUrl());

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_cart_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', $this->getCartApiUrl(), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_create_cart()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/cart.yml');

        $data =
<<<EOT
        {
            "customer": "oliver.queen@star-city.com",
            "channel": "WEB",
            "localeCode": "en_US"
        }
EOT;

        $this->client->request('POST', $this->getCartApiUrl(), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'cart/show_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_denies_getting_carts_for_non_authenticated_user()
    {
        $this->client->request('GET', $this->getCartApiUrl());

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_to_get_carts_list()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/cart.yml');

        $this->client->request('GET', $this->getCartApiUrl(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_list_carts_in_state_different_than_cart()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/order.yml');

        $this->client->request('GET', $this->getCartApiUrl(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/empty_index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_carts_deletion_for_non_authenticated_user()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $carts = $this->loadFixturesFromFile('resources/cart.yml');
        /** @var OrderInterface $cart */
        $cart = $carts['order_001'];

        $this->client->request('DELETE', $this->getCartApiUrl((string) $cart->getId()));

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_trying_to_delete_cart_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', $this->getCartApiUrl('-1'), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_delete_cart()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $carts = $this->loadFixturesFromFile('resources/cart.yml');

        /** @var OrderInterface $cart */
        $cart = $carts['order_001'];

        $this->client->request('DELETE', $this->getCartApiUrl((string) $cart->getId()), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getCartApiUrl((string) $cart->getId()), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_delete_orders_in_state_different_than_cart()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $orders = $this->loadFixturesFromFile('resources/order.yml');

        /** @var OrderItemInterface $order */
        $order = $orders['order_001'];

        $this->client->request('DELETE', $this->getCartApiUrl((string) $order->getId()), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_denies_adding_a_product_to_cart_for_non_authenticated_user()
    {
        $this->client->request('POST', $this->getCartApiUrl('1/items/'));

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_item_to_cart_without_providing_required_fields()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $carts = $this->loadFixturesFromFile('resources/cart.yml');

        /** @var OrderInterface $cart */
        $cart = $carts['order_001'];

        $this->client->request('POST', $this->getCartItemListUrl($cart), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/add_to_cart_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_adds_an_item_to_the_cart()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $carts = $this->loadFixturesFromFile('resources/cart.yml');

        /** @var OrderInterface $cart */
        $cart = $carts['order_001'];

        $data =
<<<EOT
        {
            "variant": "MUG_SW",
            "quantity": 1
        }
EOT;
        $this->client->request('POST', $this->getCartItemListUrl($cart), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/add_to_cart_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_add_item_with_negative_quantity()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $carts = $this->loadFixturesFromFile('resources/cart.yml');

        /** @var OrderInterface $cart */
        $cart = $carts['order_001'];

        $data =
<<<EOT
        {
            "variant": "MUG_SW",
            "quantity": -1
        }
EOT;
        $this->client->request('POST', $this->getCartItemListUrl($cart), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/add_to_cart_quantity_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_adds_an_item_to_the_cart_with_quantity_bigger_than_one()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $carts = $this->loadFixturesFromFile('resources/cart.yml');

        /** @var OrderInterface $cart */
        $cart = $carts['order_001'];

        $data =
<<<EOT
        {
            "variant": "MUG_SW",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', $this->getCartItemListUrl($cart), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/add_to_cart_with_bigger_quantity_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_adds_an_item_with_tracked_variant_to_the_cart()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $carts = $this->loadFixturesFromFile('resources/cart.yml');

        /** @var OrderInterface $cart */
        $cart = $carts['order_001'];

        $data =
<<<EOT
        {
            "variant": "HARD_AVAILABLE_MUG",
            "quantity": 1
        }
EOT;
        $this->client->request('POST', $this->getCartItemListUrl($cart), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/add_to_cart_hard_available_item_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_checks_if_requested_variant_is_available()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $carts = $this->loadFixturesFromFile('resources/cart.yml');

        /** @var OrderInterface $cart */
        $cart = $carts['order_001'];

        $data =
<<<EOT
        {
            "variant": "HARD_AVAILABLE_MUG",
            "quantity": 3
        }
EOT;
        $this->client->request('POST', $this->getCartItemListUrl($cart), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/add_to_cart_hard_available_item_validation_error_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_denies_updating_a_cart_item_quantity_for_non_authenticated_user()
    {
        $this->client->request('PUT', $this->getCartApiUrl('1/items/1'));

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_items_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $cartWithItems = $this->loadFixturesFromFile('resources/cart_with_items.yml');

        /** @var OrderInterface $cart */
        $cart = $cartWithItems['cart_with_items'];
        /** @var OrderItemInterface $cartItem */
        $cartItem = $cartWithItems['sw_mug_item'];

        $data =
<<<EOT
        {
            "variant": "MUG_SW"
        }
EOT;

        $this->client->request('PUT', $this->getCartItemUrl($cart, $cartItem), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/update_cart_item_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_updates_item_quantity()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $cartWithItems = $this->loadFixturesFromFile('resources/cart_with_items.yml');

        /** @var OrderInterface $cart */
        $cart = $cartWithItems['cart_with_items'];
        /** @var OrderItemInterface $cartItem */
        $cartItem = $cartWithItems['sw_mug_item'];

        $data =
<<<EOT
        {
            "quantity": 3
        }
EOT;
        $this->client->request('PUT', $this->getCartItemUrl($cart, $cartItem), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getCartApiUrl((string) $cart->getId()), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/increase_quantity_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_checks_if_requested_variant_is_available_during_quantity_update()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $cartWithItems = $this->loadFixturesFromFile('resources/cart_with_items.yml');

        /** @var OrderInterface $cart */
        $cart = $cartWithItems['cart_with_items'];
        /** @var OrderItemInterface $cartItem */
        $cartItem = $cartWithItems['hard_available_mug_item'];

        $data =
<<<EOT
        {
            "quantity": 3
        }
EOT;
        $this->client->request('PUT', $this->getCartItemUrl($cart, $cartItem), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/update_hard_available_cart_item_validation_error_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_denies_carts_item_deletion_for_non_authenticated_user()
    {
        $this->client->request('DELETE', $this->getCartApiUrl('-1/items/-1'));

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_trying_to_delete_cart_item_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $cartWithItems = $this->loadFixturesFromFile('resources/cart_with_items.yml');

        /** @var OrderInterface $cart */
        $cart = $cartWithItems['cart_with_items'];
        $url = sprintf($this->getCartApiUrl('%s/items/-1'), $cart->getId());

        $this->client->request('DELETE', $url, [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_delete_cart_item()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $cartWithItems = $this->loadFixturesFromFile('resources/cart_with_items.yml');

        /** @var OrderInterface $cart */
        $cart = $cartWithItems['cart_with_items'];
        /** @var OrderItemInterface $cartItem */
        $cartItem = $cartWithItems['hard_available_mug_item'];

        $this->client->request('DELETE', $this->getCartItemUrl($cart, $cartItem), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_resets_totals_if_cart_item_was_removed()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $cartWithItems = $this->loadFixturesFromFile('resources/cart_with_items.yml');

        /** @var OrderInterface $cart */
        $cart = $cartWithItems['cart_with_items'];
        /** @var OrderItemInterface $cartItem */
        $cartItem = $cartWithItems['hard_available_mug_item'];

        $this->client->request('DELETE', $this->getCartItemUrl($cart, $cartItem), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getCartApiUrl((string) $cart->getId()), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'cart/recalculated_items_total_response', Response::HTTP_OK);
    }

    private function getCartApiUrl(string $urlFragment = ''): string
    {
        return '/api/v1/carts/' . $urlFragment;
    }

    /**
     * @return string
     */
    private function getCartItemListUrl(OrderInterface $cart)
    {
        return sprintf($this->getCartApiUrl('%s/items/'), (string) $cart->getId());
    }

    /**
     * @return string
     */
    private function getCartItemUrl(OrderInterface $cart, OrderItemInterface $cartItem)
    {
        return sprintf($this->getCartApiUrl('%s/items/%s'), $cart->getId(), $cartItem->getId());
    }
}
