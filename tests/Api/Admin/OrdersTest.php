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

namespace Sylius\Tests\Api\Admin;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Symfony\Component\HttpFoundation\Response;

final class OrdersTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    /** @test */
    public function it_gets_an_order(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);
        $headers = $this->headerBuilder()->withJsonLdAccept()->withAdminUserAuthorization('api@example.com')->build();

        $tokenValue = 'nAWw2jewpA';

        $this->placeOrder($tokenValue);
        $this->client->request(method: 'GET', uri: '/api/v2/admin/orders/' . $tokenValue, server: $headers);
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'admin/order/get_order_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_orders_for_customer(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'customer.yaml',
            'customer_order.yaml',
        ]);

        $headers = $this
            ->headerBuilder()
            ->withJsonLdAccept()
            ->withJsonLdContentType()
            ->withAdminUserAuthorization('api@example.com')
            ->build()
        ;

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_tony'];

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/orders?customer.id=' . $customer->getId(),
            server: $headers,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/order/gets_orders_for_customer_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_a_billing_address_of_placed_order(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'order/order.yaml',
            'order/customer.yaml',
        ]);

        $headers = $this
            ->headerBuilder()
            ->withJsonLdAccept()
            ->withAdminUserAuthorization('api@example.com')
            ->build()
        ;

        /** @var AddressInterface $billingAddress */
        $billingAddress = $fixtures['billing_address'];

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/addresses/' . $billingAddress->getId(),
            server: $headers,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/order/get_billing_address_of_placed_order_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_updates_a_billing_address_of_placed_order(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'order/customer.yaml',
            'order/order.yaml',
        ]);


        $headers = $this
            ->headerBuilder()
            ->withJsonLdAccept()
            ->withJsonLdContentType()
            ->withAdminUserAuthorization('api@example.com')
            ->build()
        ;

        /** @var AddressInterface $billingAddress */
        $billingAddress = $fixtures['billing_address'];

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/admin/addresses/' . $billingAddress->getId(),
            server: $headers,
            content: json_encode([
                'firstName' => 'Updated: Adam',
                'lastName' => 'Updated: Handley',
                'company'=> 'Updated: FMŻ',
                'street' => 'Updated: Kościuszki 21',
                'countryCode' => 'Updated: FR',
                'city' => 'Updated: Bordeaux',
                'postcode' => 'Updated: 99-999',
                'phoneNumber' => 'Updated: 911213969',
                'provinceCode' => 'Updated: PL-WP',
                'provinceName' => 'Updated: wielkopolskie'
            ]),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/order/put_billing_address_of_placed_order_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_prevents_customer_update_in_billing_address_of_placed_order(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'order/order.yaml',
            'order/customer.yaml',
        ]);

        $headers = $this
            ->headerBuilder()
            ->withJsonLdAccept()
            ->withJsonLdContentType()
            ->withAdminUserAuthorization('api@example.com')
            ->build()
        ;

        /** @var AddressInterface $billingAddress */
        $billingAddress = $fixtures['billing_address'];

        /** @var CustomerInterface $customerTony */
        $customerTony = $fixtures['customer_tony'];

        /** @var CustomerInterface $customerDave */
        $customerDave = $fixtures['customer_dave'];

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/admin/addresses/' . $billingAddress->getId(),
            server: $headers,
            content: json_encode([
                'customer' => '/api/v2/admin/customers/' . $customerDave->getId(),
            ]),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_OK);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/addresses/' . $billingAddress->getId(),
            server: $headers,
        );

        $this->assertSame(
            '/api/v2/admin/customers/' . $customerTony->getId(),
            json_decode($this->client->getResponse()->getContent())->customer
        );
    }

    /** @test */
    public function it_gets_a_shipping_address_of_placed_order(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'order/customer.yaml',
            'order/order.yaml',
        ]);

        $headers = $this
            ->headerBuilder()
            ->withJsonLdAccept()
            ->withAdminUserAuthorization('api@example.com')
            ->build()
        ;

        /** @var AddressInterface $shippingAddress */
        $shippingAddress = $fixtures['shipping_address'];

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/addresses/' . $shippingAddress->getId(),
            server: $headers,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/order/get_shipping_address_of_placed_order_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_updated_a_shipping_address_of_placed_order(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'order/customer.yaml',
            'order/order.yaml',
        ]);

        $headers = $this
            ->headerBuilder()
            ->withJsonLdAccept()
            ->withJsonLdContentType()
            ->withAdminUserAuthorization('api@example.com')
            ->build()
        ;

        /** @var AddressInterface $shippingAddress */
        $shippingAddress = $fixtures['shipping_address'];

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/admin/addresses/' . $shippingAddress->getId(),
            server: $headers,
            content: json_encode([
                'firstName' => 'Updated: Julia',
                'lastName' => 'Updated: Kowalska',
                'company' => 'Updated: Błysk',
                'street' => 'Updated: Marszałkowska 10',
                'countryCode' => 'Updated: PL',
                'city' => 'Updated: Warszawa',
                'postcode' => 'Updated: 00-001',
                'phoneNumber' => 'Updated: 48222333444',
                'provinceCode' => 'Updated: PL-MA',
                'provinceName' => 'Updated: mazowieckie'
            ]),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/order/put_shipping_address_of_placed_order_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_prevents_customer_update_in_shipping_address_of_placed_order(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'order/customer.yaml',
            'order/order.yaml',
        ]);

        $headers = $this
            ->headerBuilder()
            ->withJsonLdAccept()
            ->withJsonLdContentType()
            ->withAdminUserAuthorization('api@example.com')
            ->build()
        ;

        /** @var AddressInterface $shippingAddress */
        $shippingAddress = $fixtures['shipping_address'];

        /** @var CustomerInterface $customerTony */
        $customerTony = $fixtures['customer_tony'];

        /** @var CustomerInterface $customerDave */
        $customerDave = $fixtures['customer_dave'];

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/admin/addresses/' . $shippingAddress->getId(),
            server: $headers,
            content: json_encode([
                'customer' => '/api/v2/admin/customers/' . $customerDave->getId(),
            ]),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_OK);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/addresses/' . $shippingAddress->getId(),
            server: $headers,
        );

        $this->assertSame(
            '/api/v2/admin/customers/' . $customerTony->getId(),
            json_decode($this->client->getResponse()->getContent())->customer
        );
    }
}
