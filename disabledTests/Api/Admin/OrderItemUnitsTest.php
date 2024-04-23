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

final class OrderItemUnitsTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_gets_an_order_item_unit(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);
        $order = $this->placeOrder('token');
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/order-item-units/' . $order->getItems()->first()->getUnits()->first()->getId(),
            server: $header,
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/order_item_units/get_order_item_unit_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_adjustments_for_an_order_item_unit(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
            'cart/promotion.yaml',
        ]);

        $order = $this->placeOrder('token');
        $orderItemUnit = $order->getItems()->first()->getUnits()->first();

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/order-item-units/' . $orderItemUnit->getId() . '/adjustments',
            server: $this->headerBuilder()->withJsonLdAccept()->withAdminUserAuthorization('api@example.com')->build(),
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/order_item_units/get_order_item_unit_adjustments', Response::HTTP_OK);
    }
}
