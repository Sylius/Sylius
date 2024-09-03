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

final class OrderItemsTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpAdminContext();
        $this->setUpDefaultGetHeaders();
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_gets_an_order_item(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);
        $order = $this->placeOrder('token');

        $this->requestGet('/api/v2/admin/order-items/' . $order->getItems()->first()->getId());

        $this->assertResponse($this->client->getResponse(), 'admin/order_item/get_order_item_response');
    }

    /** @test */
    public function it_gets_adjustments_for_an_order_item(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'order/order_with_item.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
            'product/product_variant.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'customer.yaml',
        ]);

        $orderItem = $fixtures['order_item'];

        $this->requestGet('/api/v2/admin/order-items/' . $orderItem->getId() . '/adjustments');

        $this->assertResponse($this->client->getResponse(), 'admin/order_item/get_order_item_adjustments');
    }

    /** @test */
    public function it_gets_adjustments_for_an_order_item_with_type_filter(): void
    {
        $this->setUpDatabase();
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'order/order_with_item.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
            'product/product_variant.yaml',
            'tax_category.yaml',
            'shipping_category.yaml',
            'customer.yaml',
        ]);

        $orderItem = $fixtures['order_item'];

        $this->requestGet(
            uri: '/api/v2/admin/order-items/' . $orderItem->getId() . '/adjustments',
            queryParameters: ['type' => 'order_promotion'],
        );

        $this->assertResponse($this->client->getResponse(), 'admin/order_item/get_order_item_adjustments_with_type_filter');
    }
}
