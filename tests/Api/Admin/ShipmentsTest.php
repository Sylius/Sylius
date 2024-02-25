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

use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Symfony\Component\HttpFoundation\Response;

final class ShipmentsTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_gets_shipments(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = 'nAWw2jewpA';
        $anotherTokenValue = 'nAWw2jexpB';

        $this->placeOrder($tokenValue);
        $this->placeOrder($anotherTokenValue);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/shipments',
            server: $this->buildHeadersWithJsonLd('api@example.com'),
        );

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'admin/shipment/get_shipments_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_shipment(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = 'nAWw2jewpA';

        $order = $this->placeOrder($tokenValue);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/shipments/' . $order->getShipments()->first()->getId(),
            server: $this->buildHeadersWithJsonLd('api@example.com'),
        );

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'admin/shipment/get_shipment_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_ships_shipment(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $order = $this->placeOrder('nAWw2jewpA');

        $this->client->request(
            method: 'PATCH',
            uri: '/api/v2/admin/shipments/' . $order->getShipments()->first()->getId() . '/ship',
            server: $this->buildHeadersWithMergePatchJson('api@example.com'),
            content: json_encode([]),
        );

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_ACCEPTED);
    }

    /** @test */
    public function it_resends_shipment_confirmation_email(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $order = $this->placeOrder('nAWw2jewpA');
        $order->getShipments()->last()->setState('shipped');

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/shipments/' . $order->getShipments()->last()->getId() . '/resend-confirmation-email',
            server: $this->buildHeadersWithJsonLd('api@example.com'),
            content: json_encode([]),
        );

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_ACCEPTED);
        $this->assertEmailCount(2);
    }

    /** @test */
    public function it_does_not_resends_shipment_confirmation_email_for_shipment_with_invalid_state(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = 'nAWw2jewpA';

        $order = $this->placeOrder($tokenValue);

        $this->client->request(
            method: 'POST',
            uri: sprintf('/api/v2/admin/shipments/%s/resend-confirmation-email', $order->getShipments()->last()->getId()),
            server: $this->buildHeadersWithJsonLd('api@example.com'),
            content: json_encode([]),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEmailCount(1);
    }

    /** @test */
    public function it_gets_adjustments_for_a_shipment(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $order = $this->placeOrder('token');
        $shipment = $order->getShipments()->first();

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/shipments/' . $shipment->getId() . '/adjustments',
            server: $this->buildHeadersWithJsonLd('api@example.com')
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/shipment/get_shipment_adjustments', Response::HTTP_OK);
    }

    /** @return array<string, string> */
    private function buildHeadersWithMergePatchJson(string $adminEmail): array
    {
        return $this
            ->headerBuilder()
            ->withMergePatchJsonContentType()
            ->withJsonLdAccept()
            ->withAdminUserAuthorization($adminEmail)
            ->build()
        ;
    }

    /** @return array<string, string> */
    private function buildHeadersWithJsonLd(string $adminEmail): array
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
