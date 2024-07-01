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

final class OrderItemUnitsTest extends JsonApiTestCase
{
    use OrderPlacerTrait;
    use ShopUserLoginTrait;

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_gets_an_order_item_unit(): void
    {
        $this->setUpDefaultGetHeaders();
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        $order = $this->placeOrder('token', $customer->getEmailCanonical());

        $this->requestGet(
            uri: '/api/v2/shop/order-item-units/' . $order->getItems()->first()->getUnits()->first()->getId(),
            headers: $this->headerBuilder()->withShopUserAuthorization($customer->getEmailCanonical())->build(),
        );

        $this->assertResponse($this->client->getResponse(), 'shop/order_item/get_order_item_unit');
    }

    /** @test */
    public function it_does_not_return_an_order_item_unit_of_another_user(): void
    {
        $this->setUpDefaultGetHeaders();
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        $order = $this->placeOrder('token', $customer->getEmailCanonical());

        $this->requestGet(
            uri: '/api/v2/shop/order-item-units/' . $order->getItems()->first()->getUnits()->first()->getId(),
            headers: $this->headerBuilder()->withShopUserAuthorization('dave@doe.com')->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_does_not_return_an_order_item_unit_being_a_guest(): void
    {
        $this->setUpDefaultGetHeaders();
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        $order = $this->placeOrder('token', $customer->getEmailCanonical());

        $this->requestGet(
            uri: '/api/v2/shop/order-item-units/' . $order->getItems()->first()->getUnits()->first()->getId(),
            headers: $this->headerBuilder()->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }
}
