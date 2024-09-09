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

use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;

final class ShipmentsTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpShopUserContext();
        $this->setUpDefaultGetHeaders();
        $this->setUpOrderPlacer();

        $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        parent::setUp();
    }

    /** @test */
    public function it_gets_shipment(): void
    {
        $order = $this->placeOrder();
        $this->requestGet(sprintf('/api/v2/shop/orders/token/shipments/%s', $order->getShipments()->first()->getId()));

        $this->assertResponseSuccessful('shop/shipment/get_shipment_response');
    }

    /** @test */
    public function it_does_not_get_another_user_shipment(): void
    {
        $order = $this->placeOrder(email: 'another_user@example.com');

        $this->requestGet(sprintf('/api/v2/shop/orders/token/shipments/%s', $order->getShipments()->first()->getId()));

        $this->assertResponseNotFound();
    }

    /** @test */
    public function it_does_not_get_the_shop_user_shipment_when_not_authenticated(): void
    {
        $order = $this->placeOrder();
        $this->disableShopUserContext();

        $this->requestGet(sprintf('/api/v2/shop/orders/token/shipments/%s', $order->getShipments()->first()->getId()));

        $this->assertResponseNotFound();
    }
}
