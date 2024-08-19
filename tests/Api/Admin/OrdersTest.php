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

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\FilterTypes;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Symfony\Component\HttpFoundation\Response;

final class OrdersTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    private const TEST_TOKEN_VALUE = 'nAWw2jewpA';

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();
        $this->setUpAdminContext();
        $this->setUpDefaultGetHeaders();

        parent::setUp();
    }

    /** @test */
    public function it_gets_all_orders(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'order/customer.yaml',
            'order/new.yaml',
        ]);

        $this->requestGet(uri: '/api/v2/admin/orders');

        $this->assertResponseSuccessful('admin/order/get_all_orders');
    }

    /** @test */
    public function it_gets_orders_filtered_by_channel(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'order/customer.yaml',
            'order/new.yaml',
        ]);

        /** @var ChannelInterface $channel */
        $channel = $fixtures['channel_mobile'];

        $this->requestGet(
            uri: '/api/v2/admin/orders',
            queryParameters: ['channel.code' => $channel->getCode()],
        );

        $this->assertResponseSuccessful('admin/order/get_orders_filtered_by_channel');
    }

    /** @test */
    public function it_gets_orders_filtered_by_different_currencies(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'order/customer.yaml',
            'order/new.yaml',
            'order/new_in_different_currencies.yaml',
        ]);

        $this->requestGet(
            uri: '/api/v2/admin/orders',
            queryParameters: ['currencyCode' => ['PLN']],
        );
        $this->assertResponseSuccessful('admin/order/get_orders_filtered_by_pln_currency_code');

        $this->requestGet(
            uri: '/api/v2/admin/orders',
            queryParameters: ['currencyCode' => ['USD']],
        );
        $this->assertResponseSuccessful('admin/order/get_orders_filtered_by_usd_currency_code');

        $this->requestGet(
            uri: '/api/v2/admin/orders',
            queryParameters: ['currencyCode' => ['PLN', 'USD']],
        );
        $this->assertResponseSuccessful('admin/order/get_orders_filtered_by_pln_and_usd_currency_codes');
    }

    /** @test */
    public function it_gets_orders_for_customer(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'order/customer.yaml',
            'order/fulfilled.yaml',
        ]);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_tony'];

        $this->requestGet(
            uri: '/api/v2/admin/orders',
            queryParameters: ['customer.id' => $customer->getId()],
        );

        $this->assertResponseSuccessful('admin/order/get_orders_for_customer');
    }

    /**
     * @test
     *
     * @dataProvider provideOrderFilterDates
     */
    public function it_gets_orders_by_period(
        string $tokenValue,
        array $checkoutsCompletedAt,
        array $requestedLimit,
        string $filename,
    ): void {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'cart.yaml',
            'channel/channel.yaml',
            'order/customer.yaml',
            'payment_method.yaml',
            'shipping_method.yaml',
        ]);

        foreach ($checkoutsCompletedAt as $checkoutCompletedAt) {
            $this->placeOrder(
                tokenValue: $tokenValue,
                checkoutCompletedAt: new \DateTimeImmutable($checkoutCompletedAt),
            );
        }

        $checkoutCompletedAt = sprintf('checkoutCompletedAt[%s]', $requestedLimit['filterType']->value);

        $this->requestGet(
            uri: '/api/v2/admin/orders',
            queryParameters: [$checkoutCompletedAt => $requestedLimit['date']],
        );

        $this->assertResponseSuccessful($filename);
    }

    private function provideOrderFilterDates(): iterable
    {
        yield 'checkoutCompletedBefore' => [
            'tokenValue' => 'firstOrderToken',
            'checkoutsCompletedAt' => [
                '2024-01-01T00:00:00+00:00',
            ],
            'requestedLimit' => [
                'filterType' => FilterTypes::Before,
                'date' => '2024-01-01T00:00:00+00:00',
            ],
            'filename' => 'admin/order/get_orders_before_date',
        ];

        yield 'checkoutCompletedStrictlyBefore' => [
            'tokenValue' => 'firstOrderToken',
            'checkoutsCompletedAt' => [
                '2024-01-01T00:00:00+00:00',
            ],
            'requestedLimit' => [
                'filterType' => FilterTypes::StrictlyBefore,
                'date' => '2024-01-01T00:00:00+00:00',
            ],
            'filename' => 'admin/order/get_orders_empty_collection',
        ];
    }

    /** @test */
    public function it_gets_an_order(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';

        $this->placeOrder($tokenValue);

        $this->requestGet(uri: '/api/v2/admin/orders/' . $tokenValue);

        $this->assertResponseSuccessful('admin/order/get_order');
    }

    /** @test */
    public function it_resends_order_confirmation_email(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';

        $this->placeOrder($tokenValue);

        $this->client->request(
            method: 'POST',
            uri: sprintf('/api/v2/admin/orders/%s/resend-confirmation-email', $tokenValue),
            server: $this->buildHeadersWithJsonLd('api@example.com'),
            content: json_encode([]),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_ACCEPTED);
        $this->assertEmailCount(2);
    }

    /** @test */
    public function it_does_not_resends_order_confirmation_email_for_order_with_invalid_state(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';

        $this->placeOrder($tokenValue);
        $this->cancelOrder($tokenValue);

        $this->client->request(
            method: 'POST',
            uri: sprintf('/api/v2/admin/orders/%s/resend-confirmation-email', $tokenValue),
            server: $this->buildHeadersWithJsonLd('api@example.com'),
            content: json_encode([]),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEmailCount(1);
    }

    /** @test */
    public function it_gets_payments_of_order(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';

        $this->placeOrder($tokenValue);

        $this->requestGet(uri: sprintf('/api/v2/admin/orders/%s/payments', $tokenValue));

        $this->assertResponseSuccessful('admin/order/get_payments_of_order');
    }

    /** @test */
    public function it_gets_shipments_of_order(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';

        $this->placeOrder($tokenValue);

        $this->requestGet(uri: sprintf('/api/v2/admin/orders/%s/shipments', $tokenValue));

        $this->assertResponseSuccessful('admin/order/get_shipments_of_order');
    }

    /** @test */
    public function it_gets_adjustments_for_order(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
            'cart/promotion.yaml',
        ]);

        $this->placeOrder(self::TEST_TOKEN_VALUE);

        $this->requestGet(uri: sprintf('/api/v2/admin/orders/%s/adjustments', self::TEST_TOKEN_VALUE));

        $this->assertResponseSuccessful('admin/order/get_adjustments_for_a_given_order');
    }

    /** @test */
    public function it_gets_adjustments_for_order_with_type_filter(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
            'cart/promotion.yaml',
        ]);

        $this->placeOrder(self::TEST_TOKEN_VALUE);

        $this->requestGet(
            uri: sprintf('/api/v2/admin/orders/%s/adjustments', self::TEST_TOKEN_VALUE),
            queryParameters: ['type' => 'order_promotion'],
        );

        $this->assertResponseSuccessful('admin/order/get_adjustments_for_a_given_order_with_type_filter');
    }

    /** @return array<string, string> */
    private function buildHeadersWithJsonLd(string $adminEmail): array
    {
        return $this
            ->headerBuilder()
            ->withJsonLdContentType()
            ->withJsonLdAccept()
            ->withAdminUserAuthorization($adminEmail)
            ->build()
            ;
    }
}
