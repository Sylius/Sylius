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

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpOrderPlacer();
    }

    /**
     * @test
     *
     * @dataProvider getIntervals
     */
    public function it_gets_fulfilled_orders_in_specific_year_statistics(string $interval): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'statistics.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $this->fulfillOrder(
            tokenValue: 'ORDER_BEFORE_START',
            productVariantCode: 'product_variant_that_costs_1000',
            quantity: 2,
            checkoutCompletedAt: new \DateTimeImmutable('2021-12-31T23:59:59'),
        );

        $this->fulfillOrder(
            tokenValue: '2022ORDER1',
            productVariantCode: 'product_variant_that_costs_1000',
            quantity: 2,
            checkoutCompletedAt: new \DateTimeImmutable('2022-07-01T23:59:59'),
        );

        $this->fulfillOrder(
            tokenValue: '2022ORDER2',
            productVariantCode: 'product_variant_that_costs_1000',
            quantity: 2,
            checkoutCompletedAt: new \DateTimeImmutable('2022-12-31T23:59:59'),
        );

        $this->fulfillOrder(
            tokenValue: '2023ORDER1',
            productVariantCode: 'product_variant_that_costs_1000',
            quantity: 2,
            checkoutCompletedAt: new \DateTimeImmutable('2023-01-01T00:00:00'),
        );

        $this->fulfillOrder(
            tokenValue: '2023ORDER2',
            productVariantCode: 'product_variant_that_costs_1000',
            quantity: 2,
            checkoutCompletedAt: new \DateTimeImmutable('2023-04-20T00:00:00'),
        );

        $this->fulfillOrder(
            tokenValue: '2024ORDER1',
            productVariantCode: 'product_variant_that_costs_1000',
            quantity: 2,
            checkoutCompletedAt: new \DateTimeImmutable('2024-01-01T00:00:00'),
        );

        $this->fulfillOrder(
            tokenValue: '2024ORDER2',
            productVariantCode: 'product_variant_that_costs_1000',
            quantity: 2,
            checkoutCompletedAt: new \DateTimeImmutable('2024-11-15T00:00:00'),
        );

        $this->fulfillOrder(
            tokenValue: 'ORDER_AFTER_END',
            productVariantCode: 'product_variant_that_costs_1000',
            quantity: 2,
            checkoutCompletedAt: new \DateTimeImmutable('2025-01-01T00:00:00'),
        );

        $parameters = [
            'channelCode' => 'WEB',
            'startDate' => '2022-01-01T00:00:00',
            'interval' => $interval,
            'endDate' => '2024-12-31T23:59:59',
        ];

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/statistics',
            parameters: $parameters,
            server: $this->headerBuilder()->withAdminUserAuthorization('api@example.com')->build(),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/statistics/get_' . $interval . '_statistics_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_does_not_get_statistics_data_for_non_admin_user(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml']);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/statistics');

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_returns_a_not_found_status_code_if_channel_does_not_exist(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml']);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/statistics',
            parameters: [
                'channelCode' => 'CHANNEL_DOES_NOT_EXIST',
                'startDate' => '2023-01-01T00:00:00',
                'interval' => 'month',
                'endDate' => '2023-12-31T23:59:59',
            ],
            server: $this->headerBuilder()->withAdminUserAuthorization('api@example.com')->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    public function getIntervals(): iterable
    {
        yield ['day'];
        yield ['month'];
        yield ['year'];
    }
}
