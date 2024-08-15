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
use Sylius\Tests\Api\Utils\ShopUserLoginTrait;

final class PaymentsTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;
    use OrderPlacerTrait;

    protected function setUp(): void
    {
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

        $header = array_merge($this->logInShopUser('oliver@doe.com'), self::CONTENT_TYPE_HEADER);

        $tokenValue = 'nAWw2jewpA';

        $order = $this->placeOrder($tokenValue, 'oliver@doe.com');

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/payments/%s', $order->getLastPayment()->getId()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/payment/get_payment',
        );
    }
}
