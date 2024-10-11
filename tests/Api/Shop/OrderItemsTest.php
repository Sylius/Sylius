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

use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Symfony\Component\HttpFoundation\Response;

final class OrderItemsTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpDefaultGetHeaders();
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_gets_order_items_created_by_a_user_authenticated_as_a_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        $this->placeOrder('token', $customer->getEmailCanonical());

        $this->requestGet(
            uri: '/api/v2/shop/orders/token/items',
            headers: $this->headerBuilder()->withShopUserAuthorization($customer->getEmailCanonical())->build(),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order_item/get_order_items_by_user',
        );
    }

    /** @test */
    public function it_gets_order_items_created_by_a_user_authenticated_as_another_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        $this->placeOrder('token', $customer->getEmailCanonical());

        $this->requestGet(
            uri: '/api/v2/shop/orders/token/items',
            headers: $this->headerBuilder()->withShopUserAuthorization('dave@doe.com')->build(),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order_item/get_empty_order_items',
        );
    }

    /** @test */
    public function it_gets_order_items_created_by_a_user_authenticated_as_a_guest(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        $this->placeOrder('token', $customer->getEmailCanonical());

        $this->requestGet(
            uri: '/api/v2/shop/orders/token/items',
            headers: $this->headerBuilder()->build(),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order_item/get_empty_order_items',
        );
    }

    /** @test */
    public function it_gets_order_items_created_by_a_guest_authenticated_as_a_guest(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        $this->placeOrder('token', 'guest@doe.com');

        $this->requestGet(
            uri: '/api/v2/shop/orders/token/items',
            headers: $this->headerBuilder()->build(),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order_item/get_order_items_by_guest',
        );
    }

    /** @test */
    public function it_gets_order_items_created_by_a_guest_authenticated_as_a_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        $this->placeOrder('token', 'guest@doe.com');

        $this->requestGet(
            uri: '/api/v2/shop/orders/token/items',
            headers: $this->headerBuilder()->withShopUserAuthorization('dave@doe.com')->build(),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order_item/get_empty_order_items',
        );
    }

    /** @test */
    public function it_gets_an_order_item_created_by_a_user_authenticated_as_a_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        $order = $this->placeOrder('token', $customer->getEmailCanonical());

        $this->requestGet(
            uri: '/api/v2/shop/orders/token/items/' . $order->getItems()->first()->getId(),
            headers: $this->headerBuilder()->withShopUserAuthorization($customer->getEmailCanonical())->build(),
        );

        $this->assertResponse($this->client->getResponse(), 'shop/order_item/get_order_item_by_user');
    }

    /** @test */
    public function it_does_not_return_an_order_item_created_by_a_user_authenticated_as_another_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        $order = $this->placeOrder('token', $customer->getEmailCanonical());

        $this->requestGet(
            uri: '/api/v2/shop/orders/token/items/' . $order->getItems()->first()->getId(),
            headers: $this->headerBuilder()->withShopUserAuthorization('dave@doe.com')->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_does_not_return_an_order_item_created_by_a_user_authenticated_as_a_guest(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        $order = $this->placeOrder('token', $customer->getEmailCanonical());

        $this->requestGet(
            uri: '/api/v2/shop/orders/token/items/' . $order->getItems()->first()->getId(),
            headers: $this->headerBuilder()->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_gets_an_order_item_created_by_a_guest_authenticated_as_a_guest(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $order = $this->placeOrder('token', 'guest@doe.com');

        $this->requestGet(
            uri: '/api/v2/shop/orders/token/items/' . $order->getItems()->first()->getId(),
            headers: $this->headerBuilder()->build(),
        );

        $this->assertResponse($this->client->getResponse(), 'shop/order_item/get_order_item_by_guest');
    }

    /** @test */
    public function it_does_not_return_an_order_item_created_by_a_guest_authenticated_as_a_user(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $order = $this->placeOrder('token', 'guest@doe.com');

        $this->requestGet(
            uri: '/api/v2/shop/orders/token/items/' . $order->getItems()->first()->getId(),
            headers: $this->headerBuilder()->withShopUserAuthorization('dave@doe.com')->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_gets_order_item_adjustments(): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
            'cart/promotion.yaml',
        ]);

        $order = $this->placeOrder('token');

        $this->requestGet(sprintf('/api/v2/shop/orders/token/items/%s/adjustments', $order->getItems()->first()->getId()));

        $this->assertResponse($this->client->getResponse(), 'shop/order_item/get_order_item_adjustments');
    }

    /** @test */
    public function it_gets_empty_order_item_adjustments_if_order_token_is_wrong(): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
            'cart/promotion.yaml',
        ]);

        $order = $this->placeOrder('token');

        $this->requestGet('/api/v2/shop/orders/WRONG_TOKEN/items/' . $order->getItems()->first()->getId() . '/adjustments');

        $this->assertResponse($this->client->getResponse(), 'shop/order_item/get_empty_order_item_adjustments');
    }

    /** @test */
    public function it_returns_nothing_if_a_user_tries_to_get_the_order_item_adjustments_of_another_user(): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'authentication/shop_user.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
            'cart/promotion.yaml',
        ]);

        $order = $this->placeOrder('token', 'oliver@doe.com');

        $this->requestGet(
            uri: '/api/v2/shop/orders/token/items/' . $order->getItems()->first()->getId() . '/adjustments',
            headers: $this->headerBuilder()->withShopUserAuthorization('dave@doe.com')->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_prevents_visitors_from_getting_the_item_adjustments_of_a_user_order(): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'authentication/shop_user.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
            'cart/promotion.yaml',
        ]);

        $order = $this->placeOrder('token', 'oliver@doe.com');

        $this->requestGet(sprintf('/api/v2/shop/orders/token/items/%s/adjustments', $order->getItems()->first()->getId()));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNAUTHORIZED);
    }
}
