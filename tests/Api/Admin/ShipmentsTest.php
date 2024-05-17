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
            server: $this->buildHeadersWithJsonLd('api@example.com'),
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/shipment/get_shipment_adjustments', Response::HTTP_OK);
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
