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
use Symfony\Component\HttpFoundation\Response;

final class OrderItemUnitsTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpDefaultGetHeaders();
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_does_not_return_an_order_item_unit(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
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

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }
}
