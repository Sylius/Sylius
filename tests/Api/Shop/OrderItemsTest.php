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

use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Sylius\Tests\Api\Utils\ShopUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class OrderItemsTest extends JsonApiTestCase
{
    use OrderPlacerTrait;
    use ShopUserLoginTrait;

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_gets_order_item_adjustments(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
            'cart/promotion.yaml',
        ]);

        $order = $this->placeOrder('token');

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/orders/token/items/' . $order->getItems()->first()->getId() . '/adjustments',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/order_item/get_order_item_adjustments', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_empty_order_item_adjustments_if_order_token_is_wrong(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
            'cart/promotion.yaml',
        ]);

        $order = $this->placeOrder('token');

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/orders/WRONG_TOKEN/items/' . $order->getItems()->first()->getId() . '/adjustments',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/order_item/get_empty_order_item_adjustments', Response::HTTP_OK);
    }
}
