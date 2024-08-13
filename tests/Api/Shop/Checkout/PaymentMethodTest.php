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

namespace Sylius\Tests\Api\Shop\Checkout;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Symfony\Component\HttpFoundation\Response;

final class PaymentMethodTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_selects_payment_method_of_order_created_by_a_user_authenticated_as_a_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['payment_method_bank_transfer'];
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];

        $tokenValue = $this->pickUpCart(email: $customer->getEmailCanonical());
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $cart = $this->updateCartWithAddress($tokenValue, $customer->getEmailCanonical());
        $cart = $this->dispatchShippingMethodChooseCommand($tokenValue, 'DHL', $cart->getShipments()->first()->getId());

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/payments/%s', $tokenValue, $cart->getLastPayment()->getId()),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->withShopUserAuthorization($customer->getEmailCanonical())->build(),
            content: json_encode([
                'paymentMethod' => $paymentMethod->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/checkout/payment_method/select_payment_method_by_user',
        );
    }

    /** @test */
    public function it_does_not_select_payment_method_of_order_created_by_a_user_authenticated_as_another_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['payment_method_bank_transfer'];
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];

        $tokenValue = $this->pickUpCart(email: $customer->getEmailCanonical());
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $cart = $this->updateCartWithAddress($tokenValue, $customer->getEmailCanonical());
        $cart = $this->dispatchShippingMethodChooseCommand($tokenValue, 'DHL', $cart->getShipments()->first()->getId());

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/payments/%s', $tokenValue, $cart->getLastPayment()->getId()),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->withShopUserAuthorization('dave@doe.com')->build(),
            content: json_encode([
                'paymentMethod' => $paymentMethod->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_does_not_select_payment_method_of_order_created_by_a_user_authenticated_as_a_guest(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['payment_method_bank_transfer'];
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];

        $tokenValue = $this->pickUpCart(email: $customer->getEmailCanonical());
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $cart = $this->updateCartWithAddress($tokenValue, $customer->getEmailCanonical());
        $cart = $this->dispatchShippingMethodChooseCommand($tokenValue, 'DHL', $cart->getShipments()->first()->getId());

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/payments/%s', $tokenValue, $cart->getLastPayment()->getId()),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->build(),
            content: json_encode([
                'paymentMethod' => $paymentMethod->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_selects_payment_method_of_order_created_by_a_guest_authenticated_as_a_guest(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['payment_method_bank_transfer'];

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $cart = $this->updateCartWithAddress($tokenValue, 'guest@doe.com');
        $cart = $this->dispatchShippingMethodChooseCommand($tokenValue, 'DHL', $cart->getShipments()->first()->getId());

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/payments/%s', $tokenValue, $cart->getLastPayment()->getId()),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->build(),
            content: json_encode([
                'paymentMethod' => $paymentMethod->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/checkout/payment_method/select_payment_method_by_guest',
        );
    }

    /** @test */
    public function it_does_not_select_payment_method_of_order_created_by_a_guest_authenticated_as_another_user(): void
    {
        $this->markTestSkipped('TODO: This test should be turn on after fixing this in previous version.');
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['payment_method_bank_transfer'];

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $cart = $this->updateCartWithAddress($tokenValue, 'guest@doe.com');
        $cart = $this->dispatchShippingMethodChooseCommand($tokenValue, 'DHL', $cart->getShipments()->first()->getId());

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/payments/%s', $tokenValue, $cart->getLastPayment()->getId()),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->withShopUserAuthorization('dave@doe.com')->build(),
            content: json_encode([
                'paymentMethod' => $paymentMethod->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_does_not_allow_to_select_payment_method_to_non_existing_payment(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['payment_method_bank_transfer'];

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $cart = $this->updateCartWithAddress($tokenValue);
        $this->dispatchShippingMethodChooseCommand($tokenValue, 'DHL', $cart->getShipments()->first()->getId());

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/payments/%s', $tokenValue, '1234'),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->build(),
            content: json_encode([
                'paymentMethod' => $paymentMethod->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_does_not_allow_to_select_payment_method_with_missing_fields(): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $cart = $this->updateCartWithAddress($tokenValue);
        $cart = $this->dispatchShippingMethodChooseCommand($tokenValue, 'DHL', $cart->getShipments()->first()->getId());

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/payments/%s', $tokenValue, $cart->getLastPayment()->getId()),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->build(),
            content: '{}',
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/checkout/payment_method/select_payment_method_with_missing_fields',
            Response::HTTP_BAD_REQUEST,
        );
    }

    /** @test */
    public function it_does_not_allow_to_select_payment_method_with_invalid_payment_method(): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $cart = $this->updateCartWithAddress($tokenValue);
        $cart = $this->dispatchShippingMethodChooseCommand($tokenValue, 'DHL', $cart->getShipments()->first()->getId());

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/payments/%s', $tokenValue, $cart->getLastPayment()->getId()),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->build(),
            content: json_encode([
                'paymentMethod' => 'invalid',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                ['propertyPath' => '', 'message' => 'The payment method with invalid code does not exist.'],
            ],
        );
    }
}
