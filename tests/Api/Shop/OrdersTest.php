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

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
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
        $this->setUpDefaultGetHeaders();
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';
        $this->placeOrder($tokenValue);
        $this->requestGet(sprintf('/api/v2/shop/orders/%s', $tokenValue));

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order/get_order',
        );
    }

    /** @test */
    public function it_does_not_allow_to_get_another_customers_order(): void
    {
        $this->setUpDefaultGetHeaders();
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';
        $this->placeOrder($tokenValue);
        $this->requestGet(
            uri: sprintf('/api/v2/shop/orders/%s', $tokenValue),
            headers: $this->headerBuilder()->withShopUserAuthorization('oliver@doe.com')->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_gets_orders(): void
    {
        $this->setUpDefaultGetHeaders();
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $this->placeOrder('nAWw2jewpA', 'oliver@doe.com');
        $this->placeOrder('nAWw2jewpB', 'oliver@doe.com');
        $this->requestGet(
            uri: '/api/v2/shop/orders',
            headers: $this->headerBuilder()->withShopUserAuthorization('oliver@doe.com')->build(),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order/get_orders',
        );
    }

    /** @test */
    public function it_does_not_allow_to_get_orders_for_guest(): void
    {
        $this->setUpDefaultGetHeaders();
        $this->requestGet('/api/v2/shop/orders');

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_gets_order_items(): void
    {
        $this->setUpDefaultGetHeaders();
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';
        $this->placeOrder($tokenValue);

        $this->requestGet(sprintf('/api/v2/shop/orders/%s/items', $tokenValue));

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order/get_order_items',
        );
    }

    /** @test */
    public function it_returns_nothing_if_visitor_tries_to_get_the_items_of_a_user_order(): void
    {
        $this->setUpDefaultGetHeaders();
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'authentication/customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';
        $this->placeOrder($tokenValue, 'oliver@doe.com');

        $this->requestGet(sprintf('/api/v2/shop/orders/%s/items', $tokenValue));

        $this->assertResponse($this->client->getResponse(), 'shop/get_empty_order_items_response');
    }

    /** @test */
    public function it_prevents_visitors_from_getting_the_adjustments_of_a_user_order(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'authentication/customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';
        $this->placeOrder($tokenValue, 'oliver@doe.com');

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA/adjustments', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_prevents_visitors_from_getting_the_item_adjustments_of_a_user_order(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'authentication/customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();

        $order = $this->createOrderWithOrderItemAdjustments($tokenValue, 'oliver@doe.com');

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA/items/'.$order->getItems()->first()->getId().'/adjustments', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }

    public function it_returns_nothing_if_a_user_tries_to_get_the_order_item_adjustments_of_another_user(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'authentication/customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $loginData = $this->logInShopUser('dave@doe.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $loginData;
        $header = array_merge($header, self::CONTENT_TYPE_HEADER);

        $tokenValue = $this->pickUpCart();

        $order = $this->createOrderWithOrderItemAdjustments($tokenValue, 'oliver@doe.com');

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA/items/'.$order->getItems()->first()->getId().'/adjustments', [], [], $header);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_gets_order_item_adjustments(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = $this->pickUpCart();

        $order = $this->createOrderWithOrderItemAdjustments($tokenValue);

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA/items/'.$order->getItems()->first()->getId().'/adjustments', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_order_item_adjustments_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_update_payment_method_on_order(): void
    {
        $this->setUpDefaultGetHeaders();
        $fixtures = $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['payment_method_bank_transfer'];

        $tokenValue = 'nAWw2jewpA';
        $this->placeOrder($tokenValue, 'oliver@doe.com');

        $this->requestGet(
            uri: sprintf('/api/v2/shop/orders/%s', $tokenValue),
            headers: $this->headerBuilder()->withShopUserAuthorization('oliver@doe.com')->build(),
        );
        $orderResponse = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/account/orders/%s/payments/%s', $tokenValue, $orderResponse['payments'][0]['id']),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->withShopUserAuthorization('oliver@doe.com')->build(),
            content: json_encode([
                'paymentMethod' => $paymentMethod->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order/update_payment_method',
        );
    }

    /** @test */
    public function it_does_not_allow_to_update_payment_method_for_cancelled_order(): void
    {
        $this->setUpDefaultGetHeaders();
        $fixtures = $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['payment_method_bank_transfer'];

        $tokenValue = 'nAWw2jewpA';
        $this->placeOrder($tokenValue, 'oliver@doe.com');
        $this->cancelOrder($tokenValue);

        $this->requestGet(
            uri: sprintf('/api/v2/shop/orders/%s', $tokenValue),
            headers: $this->headerBuilder()->withShopUserAuthorization('oliver@doe.com')->build(),
        );
        $orderResponse = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/account/orders/%s/payments/%s', $tokenValue, $orderResponse['payments'][0]['id']),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->withShopUserAuthorization('oliver@doe.com')->build(),
            content: json_encode([
                'paymentMethod' => $paymentMethod->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                ['propertyPath' => '', 'message' => 'You cannot change the payment method for a cancelled order.'],
            ],
        );
    }

    /** @test */
    public function it_does_not_allow_to_get_payment_configuration_for_invalid_payment(): void
    {
        $this->setUpDefaultGetHeaders();
        $fixtures = $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';
        $this->placeOrder($tokenValue);

        $this->requestGet(sprintf('/api/v2/shop/orders/%s/payments/%s/configuration', $tokenValue, 'invalid-payment-id'));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_does_not_allow_delete_completed_order(): void
    {
        $this->setUpDefaultGetHeaders();
        $fixtures = $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';
        $this->placeOrder($tokenValue, 'oliver@doe.com');

        $this->requestDelete(
            uri: sprintf('/api/v2/shop/orders/%s', $tokenValue),
            headers: $this->headerBuilder()->withShopUserAuthorization('oliver@doe.com')->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    private function createOrderWithOrderItemAdjustments(string $tokenValue, string $email = 'sylius@example.com'): OrderInterface
    {
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        /** @var OrderInterface $order */
        $order = $this->get('sylius.repository.order')->findCartByTokenValue($tokenValue);
        $orderItem = $order->getItems()->first();

        /** @var AdjustmentInterface $adjustment */
        $adjustment = $this->get('sylius.factory.adjustment')->createNew();

        $adjustment->setType(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT);
        $adjustment->setAmount(200);
        $adjustment->setNeutral(false);
        $adjustment->setLabel('Test Promotion Adjustment');

        $this->updateCartWithAddress($tokenValue, $email);

        $orderItem->addAdjustment($adjustment);
        $this->get('sylius.manager.order')->flush();

        return $order;
    }
}
