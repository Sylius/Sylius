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
use Webmozart\Assert\Assert;

final class OrdersTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_gets_all_orders(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'order/customer.yaml',
            'order/new.yaml',
        ]);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/orders',
            server: $this->buildHeaders('api@example.com'),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/order/get_all_orders_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_an_order(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = 'nAWw2jewpA';

        $this->placeOrder($tokenValue);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/orders/' . $tokenValue,
            server: $this->buildHeaders('api@example.com'),
        );

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'admin/order/get_order_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_adjustments_for_order(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $tokenValue = 'nAWw2jewpA';

        $this->placeOrder($tokenValue);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/orders/nAWw2jewpA/adjustments', server: $header);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/order/get_adjustments_for_a_given_order_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_orders_for_customer(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'order/customer.yaml',
            'order/fulfilled.yaml',
        ]);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_tony'];

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/orders?customer.id=' . $customer->getId(),
            server: $this->buildHeaders('api@example.com'),
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
            'order/new.yaml',
            'order/customer.yaml',
        ]);

        /** @var AddressInterface $billingAddress */
        $billingAddress = $fixtures['first_order_billing_address'];

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/addresses/' . $billingAddress->getId(),
            server: $this->buildHeaders('api@example.com'),
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
            'order/new.yaml',
        ]);

        /** @var AddressInterface $billingAddress */
        $billingAddress = $fixtures['first_order_billing_address'];

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/admin/addresses/' . $billingAddress->getId(),
            server: $this->buildHeaders('api@example.com'),
            content: json_encode([
                'firstName' => 'Updated: Adam',
                'lastName' => 'Updated: Handley',
                'company' => 'Updated: FMŻ',
                'street' => 'Updated: Kościuszki 21',
                'countryCode' => 'Updated: FR',
                'city' => 'Updated: Bordeaux',
                'postcode' => 'Updated: 99-999',
                'phoneNumber' => 'Updated: 911213969',
                'provinceCode' => 'Updated: PL-WP',
                'provinceName' => 'Updated: wielkopolskie',
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
            'order/new.yaml',
            'order/customer.yaml',
        ]);

        /** @var AddressInterface $billingAddress */
        $billingAddress = $fixtures['first_order_billing_address'];

        /** @var CustomerInterface $customerTony */
        $customerTony = $fixtures['customer_tony'];

        /** @var CustomerInterface $customerDave */
        $customerDave = $fixtures['customer_dave'];

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/admin/addresses/' . $billingAddress->getId(),
            server: $this->buildHeaders('api@example.com'),
            content: json_encode([
                'customer' => '/api/v2/admin/customers/' . $customerDave->getId(),
            ]),
        );

        $content = $this->client->getResponse()->getContent();
        Assert::notFalse($content, 'Address response content should not be empty.');

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_OK);
        $this->assertSame('/api/v2/admin/customers/' . $customerTony->getId(), json_decode($content)->customer);
    }

    /** @test */
    public function it_gets_a_shipping_address_of_placed_order(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'order/customer.yaml',
            'order/new.yaml',
        ]);

        /** @var AddressInterface $shippingAddress */
        $shippingAddress = $fixtures['first_order_shipping_address'];

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/addresses/' . $shippingAddress->getId(),
            server: $this->buildHeaders('api@example.com'),
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
            'order/new.yaml',
        ]);

        /** @var AddressInterface $shippingAddress */
        $shippingAddress = $fixtures['first_order_shipping_address'];

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/admin/addresses/' . $shippingAddress->getId(),
            server: $this->buildHeaders('api@example.com'),
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
                'provinceName' => 'Updated: mazowieckie',
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
            'order/new.yaml',
        ]);

        /** @var AddressInterface $shippingAddress */
        $shippingAddress = $fixtures['first_order_shipping_address'];

        /** @var CustomerInterface $customerTony */
        $customerTony = $fixtures['customer_tony'];

        /** @var CustomerInterface $customerDave */
        $customerDave = $fixtures['customer_dave'];

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/admin/addresses/' . $shippingAddress->getId(),
            server: $this->buildHeaders('api@example.com'),
            content: json_encode([
                'customer' => '/api/v2/admin/customers/' . $customerDave->getId(),
            ]),
        );

        $content = $this->client->getResponse()->getContent();
        Assert::notFalse($content, 'Address response content should not be empty.');

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_OK);
        $this->assertSame('/api/v2/admin/customers/' . $customerTony->getId(), json_decode($content)->customer);
    }

    /** @test */
    public function it_resends_order_confirmation_email(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = 'nAWw2jewpA';

        $this->placeOrder($tokenValue);

        $this->client->request(
            method: 'POST',
            uri: sprintf('/api/v2/admin/orders/%s/resend-confirmation-email', $tokenValue),
            server: $this->buildHeaders('api@example.com'),
            content: json_encode([]),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_ACCEPTED);
        $this->assertEmailCount(2);
    }

    /** @test */
    public function it_does_not_resends_order_confirmation_email_for_order_with_invalid_state(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = 'nAWw2jewpA';

        $this->placeOrder($tokenValue);
        $this->cancelOrder($tokenValue);

        $this->client->request(
            method: 'POST',
            uri: sprintf('/api/v2/admin/orders/%s/resend-confirmation-email', $tokenValue),
            server: $this->buildHeaders('api@example.com'),
            content: json_encode([]),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEmailCount(1);
    }

    /** @test */
    public function it_gets_payments_of_order(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = 'nAWw2jewpA';

        $this->placeOrder($tokenValue);

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/orders/%s/payments', $tokenValue),
            server: $this->buildHeaders('api@example.com'),
            content: json_encode([]),
        );

        $this->assertResponse($this->client->getResponse(), 'admin/order/get_payments_of_order_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_shipments_of_order(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = 'nAWw2jewpA';

        $this->placeOrder($tokenValue);

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/orders/%s/shipments', $tokenValue),
            server: $this->buildHeaders('api@example.com'),
            content: json_encode([]),
        );

        $this->assertResponse($this->client->getResponse(), 'admin/order/get_shipments_of_order_response', Response::HTTP_OK);
    }

    /** @return array<string, string> */
    private function buildHeaders(string $adminEmail): array
    {
        return $this
            ->headerBuilder()
            ->withJsonLdContentType()
            ->withJsonLdAccept()
            ->withAdminUserAuthorization($adminEmail)
            ->build()
        ;
    }
}
