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

final class AdjustmentsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;
    use OrderPlacerTrait;

    /** @test */
    public function it_returns_list_of_all_adjustments_for_a_given_order(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $orderToken = 'nAWw2jewpA';
        $this->placeOrder($orderToken);

        $anotherOrderToken = 'xd4w2jewpA';
        $this->placeOrder($anotherOrderToken);

        $url = sprintf('/api/v2/admin/adjustments?order.tokenValue=%s', $orderToken);

        $this->client->request(
            method: 'GET',
            uri: $url,
            server: $header,
        );

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'admin/adjustment/get_adjustments_for_a_given_order_response', Response::HTTP_OK);
    }
}
