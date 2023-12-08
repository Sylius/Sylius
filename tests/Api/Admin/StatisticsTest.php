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
use Symfony\Component\HttpFoundation\Response;

final class StatisticsTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    /** @test */
    public function it_gets_statistics_data(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        for ($i = 0; $i < 3; ++$i) {
            $this->placeOrder('ORDER_TOKEN' . $i, sprintf('customer_%s@example.com', $i));
            $this->payOrder('ORDER_TOKEN' . $i);
        }

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/statistics',
            parameters: ['channelCode' => 'WEB'],
            server: $this->headerBuilder()->withAdminUserAuthorization('api@example.com')->build(),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/statistics/get_statistics_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_does_not_get_statistics_data_for_non_admin_user(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        for ($i = 0; $i < 3; ++$i) {
            $this->placeOrder('ORDER_TOKEN' . $i, sprintf('customer_%s@example.com', $i));
            $this->payOrder('ORDER_TOKEN' . $i);
        }

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/statistics',
        );

        $this->assertResponseCode(
            $this->client->getResponse(),
            Response::HTTP_UNAUTHORIZED,
        );
    }

    /** @test */
    public function it_returns_a_bad_request_status_code_if_the_channel_code_is_not_provided(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        for ($i = 0; $i < 3; ++$i) {
            $this->placeOrder('ORDER_TOKEN' . $i, sprintf('customer_%s@example.com', $i));
            $this->payOrder('ORDER_TOKEN' . $i);
        }

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/statistics',
            server: $this->headerBuilder()->withAdminUserAuthorization('api@example.com')->build(),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/statistics/get_statistics_missing_channel_code_response',
            Response::HTTP_BAD_REQUEST,
        );
    }
}
