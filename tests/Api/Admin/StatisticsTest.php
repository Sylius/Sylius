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
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_gets_fulfilled_orders_in_specific_year_statistics(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'statistics.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $this->fulfillOrder(
            tokenValue: 'ORDER_FULFILLED_BEFORE_REQUESTED_PERIOD',
            productVariantCode: 'product_variant_that_costs_1000',
            quantity: 2,
            checkoutCompletedAt: new \DateTimeImmutable('2022-12-31T23:59:59'),
        );

        $this->fulfillOrder(
            tokenValue: 'ORDER_FULFILLED_IN_JANUARY',
            productVariantCode: 'product_variant_that_costs_1000',
            quantity: 2,
            checkoutCompletedAt: new \DateTimeImmutable('2023-01-01T00:00:00'),
        );

        $this->fulfillOrder(
            tokenValue: 'ORDER_FULFILLED_AFTER_REQUESTED_PERIOD',
            productVariantCode: 'product_variant_that_costs_1000',
            quantity: 2,
            checkoutCompletedAt: new \DateTimeImmutable('2024-01-01T00:00:00'),
        );

        $parameters = [
            'channelCode' => 'WEB',
            'startDate' => '2023-01-01T00:00:00',
            'interval' => 'month',
            'endDate' => '2023-12-31T23:59:59',
        ];

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/statistics',
            parameters: $parameters,
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
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml']);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/statistics');

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_returns_a_not_found_status_code_if_channel_does_not_exist(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml']);

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

    //     * @dataProvider invalidPeriods TODO: split date range strictly by period, last period cut to fit
    /**
     * @test
     *
     * @dataProvider missingQueryParameters
     * @dataProvider emptyQueryParameters
     * @dataProvider invalidQueryParameters
     */
    public function it_returns_a_bad_request_status_code_if_any_of_required_parameters_is_missing_empty_or_invalid(
        array $queryParameters,
    ): void {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml']);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/statistics',
            parameters: $queryParameters,
            server: $this->headerBuilder()->withAdminUserAuthorization('api@example.com')->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_BAD_REQUEST);
    }

    public function missingQueryParameters(): iterable
    {
        yield 'missing channelCode' => [
             'parameters' => [
                'startDate' => '2023-01-01T00:00:00',
                'interval' => 'month',
                'endDate' => '2023-12-31T23:59:59',
             ],
        ];

        yield 'missing startDate' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'interval' => 'month',
                'endDate' => '2023-12-31T23:59:59',
            ],
        ];

        yield 'missing interval' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-01-01T00:00:00',
                'endDate' => '2023-12-31T23:59:59',
            ],
        ];

        yield 'missing endDate' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-01-01T00:00:00',
                'interval' => 'month',
            ],
        ];

        yield 'missing all parameters' => [
            'parameters' => [],
        ];
    }

    public function emptyQueryParameters(): iterable
    {
        yield 'empty channelCode' => [
            'parameters' => [
                'channelCode' => '',
                'startDate' => '2023-01-01T00:00:00',
                'interval' => 'month',
                'endDate' => '2023-12-31T23:59:59',
            ],
        ];

        yield 'empty startDate' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '',
                'interval' => 'month',
                'endDate' => '2023-12-31T23:59:59',
            ],
        ];

        yield 'empty interval' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-01-01T00:00:00',
                'interval' => '',
                'endDate' => '2023-12-31T23:59:59',
            ],
        ];

        yield 'empty endDate' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-01-01T00:00:00',
                'interval' => 'month',
                'endDate' => '',
            ],
        ];
    }

    public function invalidQueryParameters(): iterable
    {
        yield 'invalid channelCode as float value' => [
            'parameters' => [
                'channelCode' => 1.1,
                'startDate' => '2023-01-01T00:00:00',
                'interval' => 'month',
                'endDate' => '2023-12-31T23:59:59',
            ],
        ];

        yield 'invalid startDate' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => 'INVALID_START_DATE',
                'interval' => 'month',
                'endDate' => '2023-12-31T23:59:59',
            ],
        ];

        yield 'unknown interval' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-01-01T00:00:00',
                'interval' => 'invalid',
                'endDate' => '2023-12-31T23:59:59',
            ],
        ];

        yield 'invalid endDate' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-01-01T00:00:00',
                'interval' => 'month',
                'endDate' => 'INVALID_END_DATE',
            ],
        ];

        yield 'unexpected parameter' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-01-01T00:00:00',
                'interval' => 'day',
                'endDate' => '2023-12-31T23:59:59',
                'unexpected_parameter' => 'unexpected_value',
            ],
        ];
    }

    public function invalidPeriods(): iterable
    {
        yield 'startDate is after endDate' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-01-01T00:00:00',
                'interval' => 'month',
                'endDate' => '2022-12-31T23:59:59',
            ],
        ];

        yield 'n-th date from interval is not matching the end date' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-01-01T00:00:00',
                'interval' => 'month',
                'endDate' => '2023-12-15T23:59:59',
            ],
        ];

        yield 'n-th date from interval matches the end date but not the time' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-12-01T00:00:00',
                'interval' => 'month',
                'endDate' => '2023-12-31T00:00:00',
            ],
        ];

        /** @see https://www.php.net/manual/en/class.dateperiod.php - DatePeriod::INCLUDE_END_DATE */
        yield 'n-th date from interval is exact n-times bigger than the start date' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-01-01T00:00:00',
                'interval' => 'month',
                'endDate' => '2024-01-01T00:00:00', // Supports only closed intervals
            ],
        ];

        yield 'interval is bigger than date range' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-12-01T00:00:00',
                'interval' => 'month',
                'endDate' => '2023-12-15T23:59:59',
            ],
        ];

        yield 'interval is bigger than date range and startDate is not the first day of the month' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-12-15T00:00:00',
                'interval' => 'month',
                'endDate' => '2023-12-31T23:59:59',
            ],
        ];

        yield 'timezone included in startDate' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-12-01T00:00:00+00:00',
                'interval' => 'month',
                'endDate' => '2023-12-31T23:59:59',
            ],
        ];

        yield 'timezone included in endDate' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-12-01T00:00:00',
                'interval' => 'month',
                'endDate' => '2023-12-31T23:59:59+00:00',
            ],
        ];
    }
}
