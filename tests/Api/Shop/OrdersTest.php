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

use Sylius\Bundle\ApiBundle\Provider\CompositePaymentConfigurationProviderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Sylius\Tests\Api\Utils\ShopUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class OrdersTest extends JsonApiTestCase
{
    use OrderPlacerTrait;
    use ShopUserLoginTrait;

    protected function setUp(): void
    {
        $this->setUpDefaultGetHeaders();
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_gets_only_logged_in_user_orders_excluding_guest_and_other_users_orders(): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $this->placeOrder('nAWw2jewpA', 'oliver@doe.com');
        $this->placeOrder('nAWw2jewpB', 'oliver@doe.com');
        $this->placeOrder('nAWw2jewpC', 'dave@doe.com');
        $this->pickUpCart('nAWw2jewpD');
        $this->updateCartWithAddressAndCouponCode('nAWw2jewpD', 'oliver@doe.com');
        $this->pickUpCart('nAWw2jewpE');

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
        $this->loadFixturesFromFile('channel/channel.yaml');
        $this->requestGet('/api/v2/shop/orders');

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_gets_an_order_created_by_a_user_authenticated_as_a_user(): void
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
            uri: '/api/v2/shop/orders/token',
            headers: $this->headerBuilder()->withShopUserAuthorization($customer->getEmailCanonical())->build(),
        );

        $this->assertResponse($this->client->getResponse(), 'shop/order/get_order_by_user');
    }

    /** @test */
    public function it_does_not_return_an_order_created_by_a_user_authenticated_as_another_user(): void
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
            uri: '/api/v2/shop/orders/token',
            headers: $this->headerBuilder()->withShopUserAuthorization('dave@doe.com')->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_does_not_return_an_order_created_by_a_user_authenticated_as_a_guest(): void
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
            uri: '/api/v2/shop/orders/token',
            headers: $this->headerBuilder()->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_gets_an_order_created_by_a_guest_authenticated_as_a_guest(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $this->placeOrder('token', 'guest@doe.com');

        $this->requestGet(
            uri: '/api/v2/shop/orders/token',
            headers: $this->headerBuilder()->build(),
        );

        $this->assertResponse($this->client->getResponse(), 'shop/order/get_order_by_guest');
    }

    /** @test */
    public function it_does_not_return_an_order_created_by_a_guest_authenticated_as_a_user(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $this->placeOrder('token', 'guest@doe.com');

        $this->requestGet(
            uri: '/api/v2/shop/orders/token',
            headers: $this->headerBuilder()->withShopUserAuthorization('dave@doe.com')->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_changes_payment_method_of_order_created_by_a_user_authenticated_as_a_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['payment_method_bank_transfer'];
        $order = $this->placeOrder('token', 'oliver@doe.com');

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/token/payments/%s', $order->getPayments()->first()->getId()),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->withShopUserAuthorization('oliver@doe.com')->build(),
            content: json_encode([
                'paymentMethod' => $paymentMethod->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order/change_payment_method',
        );
    }

    /** @test */
    public function it_does_not_change_payment_method_of_order_created_by_a_user_authenticated_as_another_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['payment_method_bank_transfer'];
        $order = $this->placeOrder('token', 'oliver@doe.com');

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/token/payments/%s', $order->getPayments()->first()->getId()),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->withShopUserAuthorization('dave@doe.com')->build(),
            content: json_encode([
                'paymentMethod' => $paymentMethod->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_does_not_change_payment_method_of_order_created_by_a_user_authenticated_as_a_guest(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['payment_method_bank_transfer'];
        $order = $this->placeOrder('token', 'oliver@doe.com');

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/token/payments/%s', $order->getPayments()->first()->getId()),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->build(),
            content: json_encode([
                'paymentMethod' => $paymentMethod->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_changes_payment_method_of_order_created_by_a_guest_authenticated_as_a_guest(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['payment_method_bank_transfer'];
        $order = $this->placeOrder('token', 'guest@doe.com');

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/token/payments/%s', $order->getPayments()->first()->getId()),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->build(),
            content: json_encode([
                'paymentMethod' => $paymentMethod->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order/change_payment_method',
        );
    }

    /** @test */
    public function it_does_not_change_payment_method_of_order_created_by_a_guest_authenticated_as_a_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['payment_method_bank_transfer'];
        $order = $this->placeOrder('token', 'guest@doe.com');

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/token/payments/%s', $order->getPayments()->first()->getId()),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->withShopUserAuthorization('dave@doe.com')->build(),
            content: json_encode([
                'paymentMethod' => $paymentMethod->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_update_payment_method_of_order_created_by_a_user_authenticated_as_a_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['payment_method_bank_transfer'];
        $order = $this->placeOrder('token', 'oliver@doe.com');

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/account/orders/token/payments/%s', $order->getPayments()->first()->getId()),
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
    public function it_does_not_update_payment_method_of_order_created_by_a_user_authenticated_as_another_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['payment_method_bank_transfer'];
        $order = $this->placeOrder('token', 'oliver@doe.com');

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/account/orders/token/payments/%s', $order->getPayments()->first()->getId()),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->withShopUserAuthorization('dave@doe.com')->build(),
            content: json_encode([
                'paymentMethod' => $paymentMethod->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_does_not_update_payment_method_authenticated_as_a_guest(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['payment_method_bank_transfer'];
        $order = $this->placeOrder('token', 'oliver@doe.com');

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/account/orders/token/payments/%s', $order->getPayments()->first()->getId()),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->build(),
            content: json_encode([
                'paymentMethod' => $paymentMethod->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_does_not_allow_to_update_payment_method_for_cancelled_order(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'channel/channel.yaml',
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
    public function it_gets_payment_configuration_of_order_created_by_a_user_authenticated_as_a_user(): void
    {
        $this->setCompositePaymentConfigurationProvider();
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        $order = $this->placeOrder('token', $customer->getEmailCanonical());

        $this->requestGet(
            uri: sprintf('/api/v2/shop/orders/token/payments/%s/configuration', $order->getPayments()->first()->getId()),
            headers: $this->headerBuilder()->withShopUserAuthorization($customer->getEmailCanonical())->build(),
        );

        $this->assertResponse($this->client->getResponse(), 'shop/order/get_configuration');
    }

    /** @test */
    public function it_does_not_return_a_payment_configuration_of_order_created_by_a_user_authenticated_as_another_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        $order = $this->placeOrder('token', $customer->getEmailCanonical());

        $this->requestGet(
            uri: sprintf('/api/v2/shop/orders/token/payments/%s/configuration', $order->getPayments()->first()->getId()),
            headers: $this->headerBuilder()->withShopUserAuthorization('dave@doe.com')->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_does_not_return_a_payment_configuration_of_order_created_by_a_user_authenticated_as_a_guest(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        $order = $this->placeOrder('token', $customer->getEmailCanonical());

        $this->requestGet(
            uri: sprintf('/api/v2/shop/orders/token/payments/%s/configuration', $order->getPayments()->first()->getId()),
            headers: $this->headerBuilder()->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_gets_payment_configuration_of_order_created_by_a_guest_authenticated_as_a_guest(): void
    {
        $this->setCompositePaymentConfigurationProvider();
        $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $order = $this->placeOrder('token', 'guest@doe.com');

        $this->requestGet(
            uri: sprintf('/api/v2/shop/orders/token/payments/%s/configuration', $order->getPayments()->first()->getId()),
            headers: $this->headerBuilder()->build(),
        );

        $this->assertResponse($this->client->getResponse(), 'shop/order/get_configuration');
    }

    /** @test */
    public function it_does_not_return_a_payment_configuration_of_order_created_by_a_guest_authenticated_as_a_user(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $order = $this->placeOrder('token', 'guest@doe.com');

        $this->requestGet(
            uri: sprintf('/api/v2/shop/orders/token/payments/%s/configuration', $order->getPayments()->first()->getId()),
            headers: $this->headerBuilder()->withShopUserAuthorization('dave@doe.com')->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_does_not_allow_to_get_payment_configuration_for_invalid_payment(): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
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
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
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

    /** @test */
    public function it_gets_order_adjustments(): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
            'cart/promotion.yaml',
        ]);

        $this->placeOrder('TOKEN');

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/orders/TOKEN/adjustments',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/order/get_order_adjustments', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_order_adjustments_with_type_filter(): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
            'cart/promotion.yaml',
        ]);

        $this->placeOrder('TOKEN');

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/orders/TOKEN/adjustments',
            parameters: ['type' => 'shipping'],
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/order/get_order_adjustments_with_type_filter', Response::HTTP_OK);
    }

    /** @test */
    public function it_prevents_visitors_from_getting_the_adjustments_of_a_user_order(): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';
        $this->placeOrder($tokenValue, 'oliver@doe.com');

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA/adjustments', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }

    private function setCompositePaymentConfigurationProvider(): void
    {
        $configurationProvider = $this->createMock(CompositePaymentConfigurationProviderInterface::class);
        $configurationProvider->method('provide')->willReturn([
            'clientId' => 123,
            'clientSecret' => 'secret',
            'orderId' => 111,
            'orderToken' => 'token',
        ]);
        self::getContainer()->set('Sylius\Bundle\ApiBundle\Provider\CompositePaymentConfigurationProvider', $configurationProvider);
    }
}
