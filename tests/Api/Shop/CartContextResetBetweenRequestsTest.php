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

use PHPUnit\Framework\Attributes\Test;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CartContextResetBetweenRequestsTest extends JsonApiTestCase
{
    #[Test]
    public function it_resets_the_cart_context_between_requests_in_the_same_kernel_process(): void
    {
        $this->client->disableReboot();

        $this->loadFixturesFromFiles(['channel/channel.yaml', 'cart.yaml', 'authentication/shop_user.yaml']);

        $this->client->request(method: 'GET', uri: '/en_US/cart/');

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_OK);

        $this->setUpDefaultPostHeaders();
        $this->requestPost(
            uri: '/api/v2/shop/orders',
            body: [],
            headers: $this->headerBuilder()->withShopUserAuthorization('shop@example.com')->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_CREATED);
    }
}
