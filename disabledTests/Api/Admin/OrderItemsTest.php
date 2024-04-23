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
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Symfony\Component\HttpFoundation\Response;

final class OrderItemsTest extends JsonApiTestCase
{
    use OrderPlacerTrait;
    use AdminUserLoginTrait;

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_gets_an_order_item(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);
        $order = $this->placeOrder('token');
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/order-items/' . $order->getItems()->first()->getId(),
            server: $header,
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/order_item/get_order_item_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_adjustments_for_an_order_item(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'order/order_with_item.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
            'product/product.yaml',
            'product/product_variant.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'customer.yaml',
        ]);
        $orderItem = $fixtures['order_item'];
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/order-items/' . $orderItem->getId() . '/adjustments',
            server: $header,
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/order_item/get_order_item_adjustments_response', Response::HTTP_OK);
    }
}
