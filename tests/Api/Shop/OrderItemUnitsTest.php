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
        $fixtures = $this->loadFixturesFromFiles(['authentication/customer.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        $header = array_merge($this->logInShopUser($customer->getEmailCanonical()), self::CONTENT_TYPE_HEADER);
        $order = $this->placeOrder('token', $customer->getEmailCanonical());

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/order-item-units/' . $order->getItems()->first()->getUnits()->first()->getId(),
            server: $header,
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/order_item/get_order_item_unit');
    }
}
