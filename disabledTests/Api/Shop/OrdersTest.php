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
}
