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

final class PaymentsTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpShopUserContext();
        $this->setUpDefaultGetHeaders();

        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_gets_payment_from_placed_order(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $order = $this->placeOrder('token');

        $this->requestGet(sprintf('/api/v2/shop/payments/%s', $order->getLastPayment()->getId()));

        $this->assertResponseSuccessful('shop/payment/get_payment');
    }
}
