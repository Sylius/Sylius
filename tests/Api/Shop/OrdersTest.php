<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Tests\Api\Shop;

use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Sylius\Tests\Api\Utils\ShopUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class OrdersTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_gets_an_order(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $this->updateCartWithAddress($tokenValue);

        $this->client->request(method: 'GET', uri: '/api/v2/shop/orders/nAWw2jewpA', server: self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/order/get_order_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_an_order_as_a_guest_with_a_customer_that_is_already_registered(): void
    {
        $this->loadFixturesFromFiles(['authentication/customer.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = $this->pickUpCart();

        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $this->updateCartWithAddress($tokenValue);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/orders/nAWw2jewpA',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/order/get_order_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_order_items(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();

        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/orders/nAWw2jewpA/items',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/order/get_order_items_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_order_adjustments(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();

        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/orders/nAWw2jewpA/adjustments',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/order/get_order_adjustments_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_allows_to_add_items_to_order(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $this->pickUpCart();

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/orders/nAWw2jewpA/items',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'productVariant' => '/api/v2/shop/product-variants/MUG_BLUE',
                'quantity' => 3,
            ], \JSON_THROW_ON_ERROR),
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/order/add_item_response', Response::HTTP_CREATED);
    }

    /** @test */
    public function it_does_not_get_orders_collection_for_guest(): void
    {
        $this->client->request(method: 'GET', uri: '/api/v2/shop/orders', server: self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/error/jwt_token_not_found', Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_allows_to_patch_orders_payment_method(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/customer.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $authentication = $this->logInShopUser('oliver@doe.com');
        $tokenValue = 'nAWw2jewpA';

        $this->placeOrder($tokenValue, 'oliver@doe.com');

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/orders/nAWw2jewpA',
            server: array_merge($authentication, self::CONTENT_TYPE_HEADER),
        );
        $orderResponse = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/account/orders/nAWw2jewpA/payments/%s', $orderResponse['payments'][0]['id']),
            server: array_merge($authentication, self::PATCH_CONTENT_TYPE_HEADER),
            content: json_encode([
                'paymentMethod' => '/api/v2/shop/payment-methods/CASH_ON_DELIVERY',
            ], \JSON_THROW_ON_ERROR),
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/order/updated_payment_method_on_order_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_validates_if_order_is_cancelled_when_trying_to_patch_orders_payment_method(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/customer.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $authentication = $this->logInShopUser('oliver@doe.com');
        $tokenValue = 'nAWw2jewpA';

        $this->placeOrder($tokenValue, 'oliver@doe.com');
        $this->cancelOrder($tokenValue);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/orders/nAWw2jewpA',
            server: array_merge($authentication, self::CONTENT_TYPE_HEADER),
        );
        $orderResponse = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/account/orders/nAWw2jewpA/payments/%s', $orderResponse['payments'][0]['id']),
            server: array_merge($authentication, self::PATCH_CONTENT_TYPE_HEADER),
            content: json_encode([
                'paymentMethod' => '/api/v2/shop/payment-methods/CASH_ON_DELIVERY',
            ], \JSON_THROW_ON_ERROR),
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/order/updated_payment_method_on_cancelled_order_response', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function it_creates_empty_cart_with_provided_locale(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/orders',
            server: array_merge(['HTTP_ACCEPT_LANGUAGE' => 'pl_PL'], self::CONTENT_TYPE_HEADER),
            content: '{}',
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/order/create_cart_response', Response::HTTP_CREATED);
    }

    /** @test */
    public function it_creates_empty_cart_with_default_locale(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/orders',
            server: self::CONTENT_TYPE_HEADER,
            content: '{}',
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/order/create_cart_with_default_locale_response', Response::HTTP_CREATED);
    }

    /** @test */
    public function it_allows_to_replace_orders_address(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();

        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        /** @var CountryInterface $country */
        $country = $fixtures['country_US'];

        $billingAddress = [
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'phoneNumber' => '666111333',
            'company' => 'Potato Corp.',
            'countryCode' => $country->getCode(),
            'provinceCode' => 'US-MI',
            'street' => 'Top secret',
            'city' => 'Nebraska',
            'postcode' => '12343',
        ];

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/shop/orders/nAWw2jewpA',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'email' => 'oliver@doe.com',
                'billingAddress' => $billingAddress,
            ], \JSON_THROW_ON_ERROR),
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/order/updated_billing_address_on_order_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_removes_item_from_the_cart(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $tokenValue = $this->pickUpCart();

        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->client->request('GET', sprintf('/api/v2/shop/orders/%s', $tokenValue));
        $itemId = json_decode($this->client->getResponse()->getContent(), true)['items'][0]['id'];

        $this->client->request('DELETE', sprintf('/api/v2/shop/orders/%s/items/%s', $tokenValue, $itemId));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function it_does_not_remove_item_from_the_cart_if_invalid_uri_item_parameter_passed(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $tokenValue = $this->pickUpCart();

        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->client->request('GET', sprintf('/api/v2/shop/orders/%s', $tokenValue));

        $this->client->request('DELETE', sprintf('/api/v2/shop/orders/%s/items/STRING-INSTEAD-OF-ID', $tokenValue));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function it_returns_unprocessable_entity_status_if_tries_to_remove_an_item_that_not_exist_in_the_order(): void
    {
        $this->loadFixturesFromFile('channel.yaml');

        $tokenValue = $this->pickUpCart();
        $nonExistingOrderItemId = 123;

        $this->client->request('DELETE', sprintf('/api/v2/shop/orders/%s/items/%s', $tokenValue, $nonExistingOrderItemId));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function it_prevents_from_adding_an_item_to_the_cart_if_product_variant_is_missing(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $tokenValue = 'nAWw2jewpA';

        $pickupCartCommand = new PickupCart($tokenValue);
        $pickupCartCommand->setChannelCode('WEB');
        $this->commandBus->dispatch($pickupCartCommand);

        $this->client->request(
            method: 'POST',
            uri: sprintf('/api/v2/shop/orders/%s/items', $tokenValue),
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'quantity' => 3,
            ], \JSON_THROW_ON_ERROR),
        );
        $response = $this->client->getResponse();

        $this->assertResponse(
            $response,
            'shop/order/add_item_to_cart_with_missing_product_variant',
            Response::HTTP_BAD_REQUEST,
        );
    }

    /** @test */
    public function it_prevents_from_adding_an_item_to_the_cart_if_quantity_is_missing(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $tokenValue = 'nAWw2jewpA';

        $pickupCartCommand = new PickupCart($tokenValue);
        $pickupCartCommand->setChannelCode('WEB');
        $this->commandBus->dispatch($pickupCartCommand);

        $this->client->request(
            method: 'POST',
            uri: sprintf('/api/v2/shop/orders/%s/items', $tokenValue),
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'productVariant' => 'MUG_BLUE',
            ], \JSON_THROW_ON_ERROR),
        );
        $response = $this->client->getResponse();

        $this->assertResponse(
            $response,
            'shop/order/add_item_to_cart_with_missing_quantity',
            Response::HTTP_BAD_REQUEST,
        );
    }

    /** @test */
    public function it_prevents_from_changing_an_item_quantity_if_quantity_is_missing(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $tokenValue = 'nAWw2jewpA';

        $pickupCartCommand = new PickupCart($tokenValue);
        $pickupCartCommand->setChannelCode('WEB');
        $this->commandBus->dispatch($pickupCartCommand);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/items/%s', $tokenValue, 1),
            server: self::PATCH_CONTENT_TYPE_HEADER,
            content: json_encode([]),
        );
        $response = $this->client->getResponse();

        $this->assertResponse(
            $response,
            'shop/order/add_item_to_cart_with_missing_quantity',
            Response::HTTP_BAD_REQUEST,
        );
    }

    public function it_returns_unprocessable_entity_status_if_trying_to_assign_shipping_method_to_non_existing_shipment(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'shipping_method.yaml']);

        $tokenValue = $this->pickUpCart();

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/shipments/%s', $tokenValue, '1237'),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->build(),
            content: json_encode(['shippingMethod' => 'api/v2/shop/shipping-methods/UPS']),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order/assign_shipping_method_to_non_existing_shipment_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_returns_unprocessable_entity_status_if_trying_to_change_item_quantity_if_invalid_item_id_passed(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $tokenValue = $this->pickUpCart();

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/items/%s', $tokenValue, 'invalid-item-id'),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->build(),
            content: json_encode(['quantity' => 5]),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function it_does_not_return_payment_configuration_if_invalid_payment_id_passed(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $tokenValue = $this->pickUpCart();

        $this->client->request(
            'GET',
            sprintf('/api/v2/shop/orders/%s/payments/%s/configuration', $tokenValue, 'invalid-payment-id'),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }
}
