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

    /**
     * @test
     *
     * @dataProvider invalidPeriods
     */
    public function it_returns_a_validation_error_if_period_is_invalid(array $parameters): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml']);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/statistics',
            parameters: $parameters,
            server: $this->headerBuilder()->withAdminUserAuthorization('api@example.com')->build(),
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                [
                    'propertyPath' => '',
                    'message' => 'The start date must be earlier than the end date.',
                ],
            ],
        );
    }

    /**
     * @test
     *
     * @dataProvider missingQueryParameters
     * @dataProvider emptyQueryParameters
     * @dataProvider invalidQueryParameters
     */
    public function it_returns_a_validation_error_if_any_of_required_parameters_is_missing_empty_or_invalid(
        array $queryParameters,
        array $expectedViolations,
    ): void {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml']);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/statistics',
            parameters: $queryParameters,
            server: $this->headerBuilder()->withAdminUserAuthorization('api@example.com')->build(),
        );

        $this->assertResponseViolations($this->client->getResponse(), $expectedViolations);
    }

    public function getIntervals(): iterable
    {
        yield ['day'];
        yield ['month'];
        yield ['year'];
    }

    public function missingQueryParameters(): iterable
    {
        yield 'missing channelCode' => [
             'parameters' => [
                'startDate' => '2023-01-01T00:00:00',
                'interval' => 'month',
                'endDate' => '2023-12-31T23:59:59',
             ],
            'expectedViolations' => [
                [
                    'propertyPath' => '[channelCode]',
                    'message' => 'This field is missing.',
                ],
            ],
        ];

        yield 'missing startDate' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'interval' => 'month',
                'endDate' => '2023-12-31T23:59:59',
            ],
            'expectedViolations' => [
                [
                    'propertyPath' => '[startDate]',
                    'message' => 'This field is missing.',
                ],
            ],
        ];

        yield 'missing interval' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-01-01T00:00:00',
                'endDate' => '2023-12-31T23:59:59',
            ],
            'expectedViolations' => [
                [
                    'propertyPath' => '[interval]',
                    'message' => 'This field is missing.',
                ],
            ],
        ];

        yield 'missing endDate' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-01-01T00:00:00',
                'interval' => 'month',
            ],
            'expectedViolations' => [
                [
                    'propertyPath' => '[endDate]',
                    'message' => 'This field is missing.',
                ],
                [
                    'propertyPath' => '',
                    'message' => 'The start date must be earlier than the end date.',
                ],
            ],
        ];

        yield 'missing all parameters' => [
            'parameters' => [],
            'expectedViolations' => [
                [
                    'propertyPath' => '[channelCode]',
                    'message' => 'This field is missing.',
                ],
                [
                    'propertyPath' => '[startDate]',
                    'message' => 'This field is missing.',
                ],
                [
                    'propertyPath' => '[interval]',
                    'message' => 'This field is missing.',
                ],
                [
                    'propertyPath' => '[endDate]',
                    'message' => 'This field is missing.',
                ],
                [
                    'propertyPath' => '',
                    'message' => 'The start date must be earlier than the end date.',
                ],
            ],
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
            'expectedViolations' => [
                [
                    'propertyPath' => '[channelCode]',
                    'message' => 'Please enter a code.',
                ],
            ],
        ];

        yield 'empty startDate' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '',
                'interval' => 'month',
                'endDate' => '2023-12-31T23:59:59',
            ],
            'expectedViolations' => [
                [
                    'propertyPath' => '[startDate]',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ];

        yield 'empty interval' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-01-01T00:00:00',
                'interval' => '',
                'endDate' => '2023-12-31T23:59:59',
            ],
            'expectedViolations' => [
                [
                    'propertyPath' => '[interval]',
                    'message' => 'The value you selected is not a valid choice.',
                ],
            ],
        ];

        yield 'empty endDate' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-01-01T00:00:00',
                'interval' => 'month',
                'endDate' => '',
            ],
            'expectedViolations' => [
                [
                    'propertyPath' => '[endDate]',
                    'message' => 'This value should not be blank.',
                ],
                [
                    'propertyPath' => '',
                    'message' => 'The start date must be earlier than the end date.',
                ],
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
            'expectedViolations' => [
                [
                    'propertyPath' => '[channelCode]',
                    'message' => 'The code should contain only letters, numbers, dashes ("-" symbol) and underscores ("_" symbol).',
                ],
            ],
        ];

        yield 'invalid startDate' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => 'INVALID_START_DATE',
                'interval' => 'month',
                'endDate' => '2023-12-31T23:59:59',
            ],
            'expectedViolations' => [
                [
                    'propertyPath' => '[startDate]',
                    'message' => 'The date time is not valid ISO 8601 date time in Y-m-d\TH:i:s format.',
                ],
                [
                    'propertyPath' => '',
                    'message' => 'The start date must be earlier than the end date.',
                ],
            ],
        ];

        yield 'unknown interval' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-01-01T00:00:00',
                'interval' => 'invalid',
                'endDate' => '2023-12-31T23:59:59',
            ],
            'expectedViolations' => [
                [
                    'propertyPath' => '[interval]',
                    'message' => 'The value you selected is not a valid choice.',
                ],
            ],
        ];

        yield 'invalid endDate' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-01-01T00:00:00',
                'interval' => 'month',
                'endDate' => 'INVALID_END_DATE',
            ],
            'expectedViolations' => [
                [
                    'propertyPath' => '[endDate]',
                    'message' => 'The date time is not valid ISO 8601 date time in Y-m-d\TH:i:s format.',
                ],
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
            'expectedViolations' => [
                [
                    'propertyPath' => '[unexpected_parameter]',
                    'message' => 'This field was not expected.',
                ],
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

        yield 'startDate is equal to endDate' => [
            'parameters' => [
                'channelCode' => 'WEB',
                'startDate' => '2023-01-01T00:00:00',
                'interval' => 'month',
                'endDate' => '2023-01-01T00:00:00',
            ],
        ];
    }
}
